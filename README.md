# WebTitans - Darryl's Restaurant

## Requirements
- PHP 8.3+

## Running

### Live

[WebTitans - Darryl's Restaurant](http://169.239.251.102:3341/~madiba.quansah/frontend/)

### Locally

#### Backend
By default the application makes requests to the live backend at
`http://169.239.251.102:3341/~madiba.quansah/backend/index.php`. To make it use the local backend, you need to go into
the `frontend` directory and edit the `src/script/constants.js` file.
Uncomment the `BASE_URL` and comment out the live `BASE_URL` like so:
```javascript
// const BASE_URL = "http://169.239.251.102:3341/~madiba.quansah/backend/src/index.php";
const BASE_URL = "http://localhost:8000";

```

Then navigate back to the root directory and run the following command:

```bash
php -S 127.0.0.1:8000 -t ./backend/src
```
