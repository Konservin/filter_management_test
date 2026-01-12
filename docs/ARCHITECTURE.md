# Filter Management Application – Technical Documentation
## 1. Overview
This project is a Symfony-based web application designed to manage filters composed of multiple criteria.
Each criterion consists of a type, subtype, and value, with dynamic behavior depending on the selected type. Subtype and value fields change upon changing the type.

## 2. Architectural Goals
The main goals of the architecture are:
- Clear separation between backend domain logic and frontend interactivity
- Predictable, reproducible development environment using Docker
- Minimal frontend framework usage, scoped only where needed
Non-goals:
- Full SPA architecture
- Client-side routing
- Complex state management libraries

## 3. Technology Stack
Backend
- PHP 8.x
- Symfony 6
- Doctrine ORM
- Doctrine Migrations (planned / optional depending on setup)
Frontend
- Vue 3
- TypeScript
- Webpack Encore
- Bootstrap (styling)
- jQuery Datepicker (legacy-compatible component)
Infrastructure
- Docker
- Docker Compose
- Nginx
- MySQL 8.0

## 4. Application Structure
High-level project structure:
- src/
◦ Controllers
◦ Entities
◦ Form Types
- templates/
◦ Twig templates
◦ Server-rendered forms
◦ Vue mount points
- assets/
◦ TypeScript entrypoints
◦ Vue components
- docker/
◦ Nginx configuration
- migrations/
◦ Doctrine migration files (not present at this stage)
The application follows Symfony’s conventional structure with minimal customization.

## 5. Backend Architecture
### 5.1 Domain Model
Core domain concepts:
- Filter
◦ Represents a named logical filter
◦ Owns a collection of criteria
- Criterion
◦ Belongs to a filter
◦ Defined by:
▪ type
▪ subtype
▪ value
The domain model is persisted using Doctrine ORM with entity relationships.

### 5.2 Controllers
Controllers are intentionally thin and responsible for:
- Handling HTTP requests
- Delegating persistence to Doctrine
- Rendering Twig templates or partials
- Returning appropriate HTTP status codes (including validation errors)
Modal-based editing is implemented by returning partial Twig templates, not JSON APIs, to keep the architecture simple and Symfony-native.

### 5.3 Forms & Validation
Symfony Forms are used for:
- Filter creation and editing
- Server-side validation
- CSRF protection
Validation exists on two layers:
- Client-side (Vue + HTML5 validation)
- Server-side (Symfony Form constraints)
Server-side validation is authoritative.

### 5.4 Database Schema and Relations
The application persists filter definitions and their criteria using a relational model designed around a clear parent–child relationship.
#### Entities and Tables
- Filter (filter)
  - Represents one saved filter definition.
  - Owns a collection of criteria.
- Criteria (criteria)
  - Represents one criterion row belonging to a filter.
  - Stores type + subtype + value for that row.
- Criterion Type / Subtype
  - Depending on the implementation, types/subtypes can be:
    - stored as lookup tables (recommended for a normalized schema), or
    - provided as an in-code mapping (acceptable for small scope), with values stored in criteria.type_id and criteria.subtype_id.

#### Relationships
Filter → Criteria is a one-to-many relationship:
  - One filter has many criteria
  - Each criteria belongs to exactly one filter

Relationally:
- criteria.filter_id is a foreign key referencing filter.id
- Deleting a filter should remove its criteria (either by:
  - Doctrine cascade remove / orphan removal, and/or
  - DB-level ON DELETE CASCADE)

Recommended constraints:
- criteria.filter_id is NOT NULL
- A filter must have at least one criterion (validated at application/form level)

#### Example Schema Sketch 
filter
- id (PK)
- name (string, required)
- created_at / updated_at (optional)

criteria
- id (PK)
- filter_id (FK → filter.id, NOT NULL)
- type_id (int/string, required when criterion row exists)
- subtype_id (int/string, required when criterion row exists)
- value (string, required when criterion row exists; interpretation depends on type)

#### Deletion Behavior
The model assumes criteria do not exist independently of a filter.
Therefore:
- Removing a criterion from a filter in the UI should delete it (orphan removal).
- Deleting the filter should delete all associated criteria.

In Doctrine terms this is typically implemented via:
- Filter having a OneToMany collection of Criteria
- Criteria having a ManyToOne back-reference to Filter
- with orphanRemoval=true and cascade={"persist","remove"} on the Filter.criteria association (exact settings depend on your entity code).
## 6. Frontend Architecture (Vue Integration)
### 6.1 Why Vue is Used Here
Vue is used only to manage the dynamic parts of the UI:
- Adding/removing criteria rows
- Reacting to type changes
- Dynamically switching input types (text, number, date)
- Client-side validation feedback

The rest of the application remains server-rendered.
This hybrid approach:
- avoids SPA complexity
- keeps Symfony forms relevant
- reduces JavaScript surface area

### 6.2 Vue Mount Strategy
Vue is mounted manually onto specific DOM elements rendered by Twig.
Key principles:
- Vue does not replace the page
- Vue enhances existing HTML
- Vue reads initial state from data-* attributes

This allows Symfony to remain the source of truth for:
- form structure
- naming conventions
- request handling

### 6.3 State Flow
1. Symfony renders the initial form
2. Initial data is embedded as JSON in data-* attributes
3. Vue hydrates and manages client-side interaction
4. Final form submission remains a standard HTTP POST

## 7. Asset Pipeline
Webpack Encore is used to:
- Compile TypeScript
- Compile Vue single-file components
- Bundle frontend assets
- Produce versioned builds for Symfony

No CDN or external asset hosting is required.

## 8. Docker Architecture
### 8.1 Services
The application uses three main containers:
- php
  - Runs PHP-FPM
  - Executes Symfony
  - Builds frontend assets
- nginx
  - Serves HTTP traffic
  - Forwards PHP requests to php:9000
- mysql
  - Stores application data
  
Docker Compose service names are used as internal DNS hostnames.

### 8.2 Development Philosophy
Docker is used to ensure:
- identical environment across machines
- no local PHP/MySQL/Node installations required
- easy onboarding for reviewers

User and permission mapping is handled to avoid root-owned files.

## 9. Known Limitations
- Filter names are not enforced as unique, no validation there
- No user authentication
- Minimal UI styling

These were considered outside the scope of the assignment.

## 10. Future Improvements
Possible future extensions:
- REST API for adding filter types
- Full SPA frontend
- Authentication & authorization
- Adding types and subtypes
- Improved UX and accessibility
- Automated tests
