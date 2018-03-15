# SuiteCRMIconizer

Iconizer can add icons to custom SuiteCRM modules via the use-icon command, 
which will copy all relevant files into your SuiteCRM custom module folder.

`php iconizer use-icon MyIcon MyModule /var/www/SuiteCRM`

It also allows to create complete icon sets from a single .png or gif. image 
located in the /images/input folder via the add-icon command.

`php iconizer add-icon MyIcon.png`

# Installation

Clone this repository and run `composer update`.

# Usage

### use-icon

The use-icon command is very easy to use. Simply install iconizer and run it!

`php iconizer use-icon <Icon Name> <Module Name> <SuiteCRM instance path>`

- Icon Name: needs to be one of the icons in the library. You can add your own via
the add-icon command.
- Module Name: should be the exact name of your custom SuiteCRM module
- SuiteCRM instance path: should be the absolute path to your SuiteCRM instance

### add-icon

For the add icon you'll need to have the at least the Imagick extension installed
on your system.

First place the icon to be added to the library in the images/input folder.
It should be 30x30 pixels and either .gif or .png format.

`php iconizer add-icon <Icon Name 1> <Icon Name 2>`

- Icon Name: The name of the file you intend to add, including the extension. You
can pass multiple files separated by spaces.

Once the import has concluded successfully, you may do a pull request to make your
icon available through this repository.

