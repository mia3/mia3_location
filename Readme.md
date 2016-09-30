mia3_location
=============

Location search using GoogleMaps


Changing paths of the template
------------------------------
You should never edit the original templates of an extension as those changes will vanish if you upgrade the extension.
As any extbase based extension, you can find the templates in the directory ```Resources/Private/```.

If you want to change a template, copy the desired files to the directory where you store the templates.
This can be a directory in ```fileadmin``` or a custom extension. Multiple fallbacks can be defined which makes it far easier to customize the templates.

```
plugin.tx_mia3location {
    view {
        templateRootPaths >
        templateRootPaths {
            0 = EXT:mia3_location/Resources/Private/Templates/
            1 = fileadmin/templates/ext/news/Templates/
        }
        partialRootPaths >
        partialRootPaths {
            0 = EXT:mia3_location/Resources/Private/Partials/
            1 = fileadmi`n/templates/ext/news/Partials/
        }
        layoutRootPaths >
        layoutRootPaths {
            0 = EXT:mia3_location/Resources/Private/Layouts/
            1 = fileadmin/templates/ext/news/Layouts/
        }
    }
}
```

Change the templates using TypoScript constants
-----------------------------------------------
You can use the following TypoScript in the  **constants** to change
the paths

```
plugin.tx_mia3location {
   view {
       templateRootPath = fileadmin/templates/ext/mia3_location/Templates/
       partialRootPath = fileadmin/templates/ext/mia3_location/Partials/
       layoutRootPath = fileadmin/templates/ext/mia3_location/Layouts/
   }
}
```

Creating a new Release/Tag
--------------------------

1. install [deployer](http://deployer.org/)
2. create release by using ```dep release:patch```, ```dep release:minor``` or ```dep release:major```