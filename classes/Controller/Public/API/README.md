All API requests should be routed to the Frontend controller. it will then
determine the version from the "Accepts:" header in the request and route to
the appropriate versioned controller. Versions are named "inward" meaning a
version of 1.2.5.27 would be routed to Controller/Public/API/1/2/5/27 and have
the class name of Controller_Public_API_1_2_5_27. This is due to the limitation on
characters in class names and in how Kohana autoloads.
