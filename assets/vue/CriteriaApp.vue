<template>
  <div ref="rootEl">
    <table class="table">
      <thead>
      <tr>
        <th>Type</th>
        <th>Subtype</th>
        <th>Value</th>
        <th></th>
      </tr>
      </thead>

      <tbody>
      <tr v-for="(row, i) in rows" :key="row.key">
        <td>
          <select
              class="form-select"
              v-model="row.typeId"
              :name="fieldName(i, 'type')"
              @change="onTypeChange(row)"
              required
          >
            <option value="">Select</option>
            <option v-for="t in types" :key="t.id" :value="String(t.id)">
              {{ t.name }}
            </option>
          </select>
        </td>

        <td>
          <select
              class="form-select"
              v-model="row.subtypeId"
              :name="fieldName(i, 'subtype')"
              required
          >
            <option value="">Select</option>
            <option v-for="s in row.subtypes" :key="s.id" :value="String(s.id)">
              {{ s.name }}
            </option>
          </select>
        </td>

        <td>
          <input
              class="form-control"
              v-model="row.value"
              :name="fieldName(i, 'value')"
              :type="row.inputType"
              :placeholder="row.placeholder"
              :ref="el => setValueRef(el, row)"
              :pattern="row.inputType === 'number' ? undefined : '.*\\S.*'"
              title="Value is required"
              required
          />
        </td>

        <td>
          <button class="btn btn-danger btn-sm" type="button" @click="remove(i)">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>
      </tbody>
    </table>

    <button class="btn btn-primary" type="button" @click="add()">
      Add criteria
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from "vue";

const rootEl = ref(null);

const props = defineProps({
  initial: { type: Array, default: () => [] },
  types: { type: Array, default: () => [] },
});

const rows = ref(
    props.initial.map((r) => ({
      key: crypto.randomUUID(),
      typeId: r.typeId != null ? String(r.typeId) : "",
      subtypeId: r.subtypeId != null ? String(r.subtypeId) : "",
      value: r.value ?? "",
      subtypes: [],              // will be fetched
      inputType: "text",
      placeholder: "",
    }))
);

const valueInputRefs = new Map();

function setValueRef(el, row) {
  if (el) {
    valueInputRefs.set(row.key, el);
  }
}

function fieldName(i, field) {
  return `filters[criteria][${i}][${field}]`;
}

function add() {
  // Remove server-side validation error if present
  const modal = rootEl.value?.closest(".modal");
  const error = modal?.querySelector(".js-form-errors");
  if (error) error.remove();

  rows.value.push({
    key: crypto.randomUUID(),
    typeId: "",
    subtypeId: "",
    value: "",
    subtypes: [],
    inputType: "text",
    placeholder: "",
  });
}

function remove(i) {
  rows.value.splice(i, 1);
}

async function loadSubtypes(row, { resetSubtype = true } = {}) {
  if (!row.typeId) {
    row.subtypes = [];
    if (resetSubtype) row.subtypeId = "";
    return;
  }

  // only reset when user actively changes Type
  if (resetSubtype) row.subtypeId = "";

  const res = await fetch(`/api/subtypes/${row.typeId}`);
  const data = await res.json();

  // normalize ids to strings for <option value="">
  row.subtypes = data.map((s) => ({ ...s, id: String(s.id) }));
}

async function applyValueType(row) {
  destroyDatepicker(row);
  // No type selected => fallback to plain text
  if (!row.typeId) {
    row.inputType = "text";
    row.placeholder = "";
    return;
  }

  try {
    const vtRes = await fetch(`/api/valuetype/${row.typeId}`);
    const vt = await vtRes.json();

    // Be tolerant to different API shapes.
    // Common shapes:
    //  - { valueType: "int" | "string" | "date" }
    //  - { id: 1 } where 1=int, 2=string, 3=date (adjust if yours differs)
    const raw = vt?.valueType ?? vt?.type ?? vt?.id ?? vt;

    const normalized =
        typeof raw === "string" ? raw.toLowerCase() : Number(raw);

    if (normalized === "int" || normalized === "integer" || normalized === 1) {
      row.inputType = "number";
      row.placeholder = "";
    } else if (normalized === "date" || normalized === 3) {
      // Keep it as text unless you switch to <input type="date">.
      // Using text preserves your dd.mm.yyyy format.
      row.inputType = "text";
      row.placeholder = "dd.mm.yyyy";
      await initDatepicker(row);
    } else {
      row.inputType = "text";
      row.placeholder = "";
    }
  } catch (e) {
    // If the API fails, don't block the UI.
    row.inputType = "text";
    row.placeholder = "";
  }
}

async function onTypeChange(row) {
  // User changed type: reset subtype + refresh subtype/value metadata
  await loadSubtypes(row, { resetSubtype: true });
  await applyValueType(row);

  // Optional: clear the value when type changes
  // row.value = "";
}

onMounted(async () => {
  // hydrate subtype options + value type for existing rows (EDIT form)
  for (const row of rows.value) {
    if (row.typeId) {
      await loadSubtypes(row, { resetSubtype: false });
      await applyValueType(row);
    }
  }
});

function destroyDatepicker(row) {
  const el = valueInputRefs.get(row.key);
  if (!el) return;

  if ($(el).data("datepicker")) {
    $(el).datepicker("destroy");
  }
}

async function initDatepicker(row) {
  await nextTick(); // wait until DOM reflects inputType change

  const el = valueInputRefs.get(row.key);
  if (!el) return;

  destroyDatepicker(row);

  $(el).datepicker({
    format: "dd.mm.yyyy",
    autoclose: true,
    todayHighlight: true,
  });
}
</script>
