# PHP-Uploader
Simple web-based file uploader.

##### Key features:
- drag-and-drop support
- files queue
- displaying upload progress
- direct links for files
- extensions blacklist
- easy to install and configure

##### Minimum installation:
- put the files from this repository into a work folder on your php server
- set the "web" folder as a public folder
- done

##### Configuration:
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