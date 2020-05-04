# camagru
Hive web-dev project. PHP/vanilla JS

# Dependencies
* PHP (>=7.1.0?) & PHP PDO extension (bundled)
* PHP MySQL & GD extensions (e.g. `apt-get install php7.3-mysql php7.3-gd` on Debian)
* MySQL (can probably be changed for another SQL DB, see `config/database.php`)

# Requirements/Features
- [ ] No error/log messages anywhere
- [x] Tech: PHP backend, PDO database driver, no frameworks/libraries except pure CSS ones
- [ ] Firefox >= 41 and Chrome >= 46 compatibility
- index.php must be in directory root

## Security
- [x] Passwords must be encrypted
- [x] No possibility of SQL injection
- [x] Sanitise user input/output
- [ ] Disallow uploading of 'unwanted' content

## User account features
- [x] Mandate email validity and password complexity
- [ ] Email validation through one-time link
- [ ] 'Forgot password' feature
- [x] Possibility to log out on any page
- [x] Ability to change username, email, password and email notification state
- [ ] Ability to receive email on new comments to user's posts

## Post/gallery features
- [x] Ability to comment on posts
- [ ] Ability to like posts
- [x] Gallery pagination
- [x] Ability to delete posts

## Capture features
- [x] Only allow logged in users to access the page
- [x] Editing section and gallery section
- [ ] Allow user to select filter
- [ ] Capture/(upload?) button must be inactive if no filter is selected
- [x] Filter must be superposed onto the image in the back-end
- [x] Allow uploading image file from computer

