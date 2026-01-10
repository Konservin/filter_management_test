/*
 * Main frontend entry (TypeScript)
 */
console.log("APP.TS LOADED", new Date().toISOString());

import { createApp, type App as VueApp } from "vue";
import CriteriaApp from "./vue/CriteriaApp.vue";

import "bootstrap";
import { Modal } from "bootstrap";

import $ from "jquery";
// bootstrap-datepicker expects global jQuery
declare global {
    interface Window {
        $: typeof $;
        jQuery: typeof $;
        valueTypes?: Record<string, number>;
    }

    // optional: store vue refs on DOM element
    interface HTMLElement {
        __vue_app__?: VueApp;
        __vue_vm__?: unknown;
    }
}
window.$ = $;
window.jQuery = $;

import "bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js";

import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-icons/font/bootstrap-icons.css";
import "bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css";
import "./app.css";

/**
 * Optional: valueTypes map
 * If you no longer use window.valueTypes anywhere, you can delete this entire block.
 */
const valueTypes: Record<string, number> = {};
fetch(`/api/filtervalues`)
    .then((response) => response.json() as Promise<Array<{ type: string; id: number }>>)
.then((data) => {
    data.forEach((subtype) => {
        valueTypes[subtype.type] = subtype.id;
    });
})
    .catch((error) => console.error("Error fetching value types:", error));
window.valueTypes = valueTypes;

/**
 * Vue island mount
 */
function mountCriteriaVue(root: ParentNode = document): void {
    const el = root.querySelector<HTMLElement>("#criteria-app");
    if (!el) return;

    // Prevent double-mount on the same element instance
    if (el.__vue_app__) return;

    const initialRaw: unknown = JSON.parse(el.dataset.initial || "[]");
    const typesRaw: unknown = JSON.parse(el.dataset.types || "[]");

    const initial = Array.isArray(initialRaw) ? initialRaw : [];
    const types = Array.isArray(typesRaw) ? typesRaw : Object.values(typesRaw as Record<string, unknown>);

    const app = createApp(CriteriaApp as any, { initial, types });
    el.__vue_app__ = app;
    el.__vue_vm__ = app.mount(el);
}

/**
 * Helper: fetch HTML and inject into a container
 */
async function fetchIntoContainer(url: string, containerEl: HTMLElement): Promise<{ res: Response; html: string }> {
    const res = await fetch(url, {
        headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    const html = await res.text();
    containerEl.innerHTML = html;
    mountCriteriaVue(containerEl);
    return { res, html };
}

type AfterRenderFn = (() => void) | undefined;

type SubmitArgs = {
    form: HTMLFormElement;
    containerEl: HTMLElement;
    afterRender?: AfterRenderFn;
};

/**
 * Helper: submit form via fetch and handle 422 re-render
 * - On 422: inject returned form HTML + re-mount Vue + call afterRender() to re-wire handlers
 * - On success: reload
 */
async function submitFilterForm({ form, containerEl, afterRender }: SubmitArgs): Promise<void> {
    const formData = new FormData(form);

    const res = await fetch(form.action, {
        method: form.method,
        body: formData,
        credentials: "same-origin",
        headers: { "X-Requested-With": "XMLHttpRequest" },
    });

    const html = await res.text();

    if (res.status === 422) {
        containerEl.innerHTML = html;
        mountCriteriaVue(containerEl);
        if (typeof afterRender === "function") afterRender();
        return;
    }

    // success: simplest is reload
    location.reload();
}

type WireModalSaveArgs = {
    containerEl: HTMLElement;
    saveBtnId: string;
    actionUrl?: string;
    afterRender?: AfterRenderFn;
};

/**
 * Reusable: wire a save button for a modal container.
 * Re-queries the form on every click (no stale references).
 * Re-wires itself after 422 re-render via afterRender callback.
 */
function wireModalSave({ containerEl, saveBtnId, actionUrl, afterRender }: WireModalSaveArgs): void {
    let saveBtn = document.getElementById(saveBtnId) as HTMLButtonElement | null;
    if (!saveBtn) {
        console.error(`${saveBtnId} not found`);
        return;
    }

    // Remove old listeners by replacing node
    saveBtn.replaceWith(saveBtn.cloneNode(true));
    saveBtn = document.getElementById(saveBtnId) as HTMLButtonElement | null;
    if (!saveBtn) return;

    saveBtn.addEventListener("click", async (e: MouseEvent) => {
        e.preventDefault();

        const form = containerEl.querySelector("form") as HTMLFormElement | null;
        if (!form) {
            console.error("Modal form not found in container");
            return;
        }

        // Make action explicit and absolute
        if (actionUrl) form.setAttribute("action", actionUrl);

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        try {
            await submitFilterForm({ form, containerEl, afterRender });
        } catch (err) {
            console.error("Error submitting modal form:", err);
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    // If criteria-app exists on page (non-modal), mount it.
    mountCriteriaVue(document);

    /**
     * NEW FILTER MODAL
     */
    const filterModal = document.getElementById("filterModal");
    const filterFormContainer = document.getElementById("filterFormContainer");

    const wireNewSave = (): void => {
        if (!filterFormContainer) return;
        wireModalSave({
            containerEl: filterFormContainer,
            saveBtnId: "save-filter-btn",
            actionUrl: "/filter/new/modal",
            afterRender: wireNewSave,
        });
    };

    if (filterModal && filterFormContainer) {
        filterModal.addEventListener("show.bs.modal", async () => {
            try {
                await fetchIntoContainer("/filter/new/modal", filterFormContainer);
                wireNewSave();
            } catch (err) {
                console.error("Error loading new filter form:", err);
            }
        });
    }

    /**
     * EDIT FILTER MODAL (delegated)
     */
    const editFilterFormContainer = document.getElementById("editFilterFormContainer");

    const wireEditSave = (): void => {
        if (!editFilterFormContainer) return;
        const filterId = editFilterFormContainer.dataset.filterId;
        if (!filterId) {
            console.error("Edit container missing data-filter-id");
            return;
        }

        wireModalSave({
            containerEl: editFilterFormContainer,
            saveBtnId: "saveEditFilterBtn",
            actionUrl: `/filter/edit/modal/${filterId}`,
            afterRender: wireEditSave,
        });
    };

    document.addEventListener("click", async (event: MouseEvent) => {
        const target = event.target as HTMLElement | null;
        if (!target) return;

        const btn = target.closest(".edit-filter") as HTMLElement | null;
        if (!btn) return;

        event.preventDefault();
        if (!editFilterFormContainer) return;

        const filterId = btn.getAttribute("data-filter-id");
        if (!filterId) return;

        try {
            editFilterFormContainer.dataset.filterId = String(filterId);
            await fetchIntoContainer(`/filter/edit/modal/${filterId}`, editFilterFormContainer);
            wireEditSave();
        } catch (err) {
            console.error("Error loading edit filter form:", err);
        }
    });

    /**
     * DELETE CONFIRM MODAL
     */
    let rowToDelete: HTMLElement | null = null;
    let filterIdToDelete: string | null = null;

    document.addEventListener("click", (event: MouseEvent) => {
        const target = event.target as HTMLElement | null;
        if (!target) return;

        const removeBtn = target.closest(".remove-filter") as HTMLElement | null;
        if (!removeBtn) return;

        event.preventDefault();

        rowToDelete = removeBtn.closest(".criteria-item") as HTMLElement | null;
        filterIdToDelete = removeBtn.getAttribute("data-bs-target");

        const confirmModalEl = document.getElementById("confirmDeleteModal");
        if (!confirmModalEl) return;

        const confirmModal = new Modal(confirmModalEl);
        confirmModal.show();
    });

    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn") as HTMLButtonElement | null;
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener("click", async () => {
            if (!filterIdToDelete) return;

            try {
                const res = await fetch(`/filter/delete/${filterIdToDelete}`, {
                    method: "DELETE",
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                });

                if (res.ok) {
                    if (rowToDelete) rowToDelete.remove();
                } else {
                    console.error("Failed to delete filter");
                }
            } catch (err) {
                console.error("Error deleting filter:", err);
            } finally {
                const confirmModalEl = document.getElementById("confirmDeleteModal");
                if (confirmModalEl) {
                    const confirmModal = Modal.getInstance(confirmModalEl);
                    if (confirmModal) confirmModal.hide();
                }
                rowToDelete = null;
                filterIdToDelete = null;
            }
        });
    }
});
