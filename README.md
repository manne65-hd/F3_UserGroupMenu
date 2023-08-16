HowTo: require package while in DEV-Mode

composer update manne65hd/f3_usergroupmenu --prefer-source -W

Required Config in composer.json of the project where you use the DEV-Package
"require": {
    "manne65hd/f3_usergroupmenu": "@dev"
},

"repositories": [
    {
        "type": "path",
        "url": "../../packagedev_manne65hd/f3_usergroupmenu",
        "options": {
            "symlink": true
        }
    }
],

"minimum-stability": "dev",
"prefer-stable": false
