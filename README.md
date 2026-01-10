Welcome to filters test site!

Recommended server stack:

* OS: Linux (Ubuntu 22.04+ recommended) / macOS / Windows with WSL2
* Web Server: Nginx (configured in Docker) or Apache (if running manually)
* PHP Version: PHP 8.1+ (Required by composer.json)
* Database: MySQL 8.0 (Configured in Docker)
* Node.js: Node.js 16+ (Required for Webpack Encore)
* Package Manager: Yarn or NPM (for frontend assets)
* Docker Engine 19.03+

To install the application in Linux, please follow these steps:

1. Open the Linux terminal
2. download the repository:
   (in Terminal) "git clone git@github.com:Konservin/filter_management_test.git"
3. move to repository directory:
   (in Terminal) "cd filter_management_test"
4. Initialize the application:
   (in Terminal) "make setup"
5. Navigate to project URL in Chrome: "http://localhost:8090"

If you run into any permission issues, try pasting into Terminal:
"make reload_dev".
If issues persist (local setup can be tricky at times), please contact me at ervin.bernhardt@gmail.com or (+372) 58242472. Reacting to English, Estonian, Finnish, Russian and French requests.

Features:

1. Edit existing filters by pressing the "Edit" button next to each filter
2. Add new filter
3. Change new filter mode from Modal to FullScreen mode
4. Add new criteria
5. Add new criteria to new or existing filters
6. Delete criteria from new or existing filters
7. Save new or existing criteria
8. Toggle criteria subtype and value depending on criteria type

Issues:

1. Can add any number of filters with the same name, needs fixing (not specified in task specs)
2. Saving filter with no criteria redirects to non-modal form
3. Scrolling of form elements not supported in non-modal form (not requested in task specs, not critical)
4. Native bootstrap datepicker is not too aesthetical, needs restyling
