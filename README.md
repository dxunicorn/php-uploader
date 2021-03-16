# PHP-Uploader
Simple web-based file uploader.

### Key features:
- drag-and-drop support
- files queue
- displaying upload progress
- direct links for files
- extensions blacklist
- easy to install and configure

### System Requirements
- PHP version 5.2.0 and higher
- PHP-JSON extension

### Installation:
- put the files from "web" folder into a work folder on your php server
- check file permissions for "upload" folder
- done

### Configuration:
You can edit the config.ini file:
- ***name*** 
Site name (will be displayed in the page title)
- ***max_upload_size***
Size limit of the file (in bytes). Should be no more than the system value (0 - the default server settings are used)
- ***blacklist***
Blacklist of extensions. Such files will be uploaded to the server, but they will be assigned the *safe_extension* extension
- ***safe_extension***
Safe extension to be added to the file from the blacklist
- ***upload_path***
The folder in which the uploaded files will be placed
- ***messages***
Messages used in templates. Required for localization