kohana-babble
=============
A RESTful API module for the Kohana PHP framework aiming to adhere to all of
the tenets of the REST architecture This is also a learning experience for me.
=)

## Version 0.3.0
This is release 0.3.0 of kohana-babble. See the
[changelog](https://github.com/shideon/kohana-babble/blob/0.3.0/master/CHANGELOG.md)
for what this version includes.

To see recent developments of this project, check out the latest /develop
branch on [github](https://github.com/shideon/kohana-babble).

## Installation
1. Clone this repository to APPPATH/modules/babble.
2. Include it in your application/bootstrap.php file.
3. Make version 1 of the API. There's a template at misc/babble-version/1. I recommend
putting this in your application directory like so. `cp -R misc/babble-versions <YOUR APP PATH>/`
this way you have a directory for all future versions of your api. An API version will be loaded
as a Kohana module. Which one is loaded depends on the Accept header, Content-Type header, or the
babble.current_version value in the config.
4. Copy the babble config to your app `cp config/babble.php <YOUR APP PATH>/config/`.
5. Add a line to your config to include version 1 of the api. The config variable is `babble.versions`. The key
is the version and the value is the path to the version.
6. By default, API controllers are accessed via /api/_controller_(/_id_). There's a front controller that's hit
at classes/Controller/Public/APIFrontend.php. It makes an internal HMVC request to the <controller>. All 
API controllers should be located in classes/Controller/Public/API/.
7. An API controller should extend Controller_API or Controller_API_Model which will be documented more in the future.
8. More docs to come...