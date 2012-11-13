# Versions
Babble supports versions. At present, versions are Kohana modules called
"version modules". Due to architectural considerations, this may change but for
now we'll document what we have.

[!!] If you haven't done so yet, it's suggested that you create a directory in your
application to house your different API versions and include version 1. You
should follow the instructions in the [Getting Started >
Configuration](http://pilot.xxx/guide/babble/gettingstarted#configuration)
section to do this.

## Requesting or Passing a Version
Versions are passed in media types in the
[Accept](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1) header
and/or the
[Content-Type](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.17)
header. E.g., application/vnd.yourname-v3+json. Babble will pull the version "3"
from this media type, see if it has a matched key in the `versions` config
value, if so, Babble will look for a class at
classes/API/MediaType/Driver/Vnd/Yourname/JSON.php in the version 3 module path
as defined in the `versions` config value. If a class is found, this module will
be loaded as a kohana module (meaning that its controllers will be used for the
requests as well if they exist).

So which module gets loaded if the Content-Type and Accept headers both include
a Babble version? The Content-Type takes precedence so its version would be loaded as a
version module. However, the media type class in the version module that matches the Accept header version
would still be used. 

# Versioning Default Media Types???
What if I don't want to use a vendor media type just to pass a version? Meaning
I want to respond with application/json but I want to pass version 3 to load my
version 3 version module? The answer is that you still have to pass a vendor
media type (i.e., application/vnd.myname-v3+json). You can either create a
custom media type that matches this type or an easier option is to use a
[Default Media
Type](mediatypes#specifying-default-media-types).

These are things that have prompted more thought and they may be changed..

# What Version Module Got Loaded?
You can use [Logging](logging) to see what version(s) were loaded.

Next Section: [Logging](logging)
