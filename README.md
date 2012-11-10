kohana-babble
=============
A RESTful API framework module for the Kohana PHP framework aiming to adhere to all of
the tenets of the REST architectural style. This is also a learning experience for me.
=)

## Version 0.4.0
This is release 0.4.0 of kohana-babble. See the
[changelog](https://github.com/shideon/kohana-babble/blob/0.4.0/master/CHANGELOG.md)
for what this version includes.

To see recent developments of this project, check out the latest /develop
branch on [github](https://github.com/shideon/kohana-babble).

## Installation & Configuration
1. Clone this repository to APPPATH/modules/babble and [enable the module](http://kohanaframework.org/3.3/guide/kohana/modules#enabling-modules).
2. Make a directory to hold your API versions and create version 1. There is a directory at misc/babble-versions that holds version 1 already for you so simply copy that directory to your APPPATH. For example, on a *nix machine use `cp -R misc/babble-versions APPPATH/`. Versions are loaded as Kohana modules to take advantage of Kohana's cascading file system, however, not all of them are loaded in a request. Which version will be loaded can be set by the `babble.current_version` config value or by the 'Accept' or 'Content-Type' header passed in the request. How this works will be documented more soon.
3. Copy the babble config to your app and add version 1 to your config. The `babble.versions` config value sets all versions of your api so add version 1 to it. The key is the version # and the value is the path to the version.
4. Set the APIs current version using the `babble.current_version` config value. For now, you should set this to version 1.
5. By default, API controllers are accessed via http://example.com/api/controller(/id). There's a front controller that's hit first at classes/Controller/Public/APIFrontend.php. It then makes an internal HMVC request to the controller. All public API controller classes should be located in classes/Controller/Public/API/ and should extend either Controller_API or Controller_API_Model. This will be documented more in the future.
6. More docs to come...
