This project is a single PHP file that can be uploaded to a webserver. It will then allow browsing and editing of files using an HTML5 editor (no browser plugins required).

The purpose of this project is to allow code editing/deleting/uploading on shared web space where FTP or other means of editing is unavailable or not practical (such as a Chromebook).

## Advantages over FTP
* No IDE software needs to be installed
* No complex firewall rules
* HTML5 only - perfect for Chrome OS

## Disadvantages over FTP
* Limited functionality
* Must upload php script to begin with

## Installation
Upload the editor.php file (in the dist/ directory) to the root of your web space then point your browser to http://yoursite.com/editor.php and login to edit files. Default password is 'admin'.

### Install - via SSH
```
cd public_html
wget https://raw.githubusercontent.com/simon-thorpe/editor/master/dist/editor.php
```

### Install - via FTP
* Upload editor.php to the root of your web directory

### Install - via CMS
Some content management systems allow creating simple text files. All you need to do is create a file editor.php (or any name you like) then paste the contents of dist/editor.php into that file.

# Building from Source
```
git clone https://github.com/simon-thorpe/editor.git
cd editor
npm install
# edit dev files in src dir
grunt
# built file is in dist dir
```
