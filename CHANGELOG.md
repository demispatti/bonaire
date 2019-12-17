## Version 0.7.4
### Changed
- Optimized for WordPress version 5.3
- Fixed scrolling issues with older IE versions

## Version 0.7.3
### Changed
- Fixed scrolling issues on iFrames
### Removed
- Removed default scrollbar with nicescroll effects

## Version 0.7.2
### Changed
- Set default value for "grab cursor" to false

## Version 0.7.1
### Added
- Added an option to enable / disable the scrollTop button on mobile devices
- Added four predefined sizes for the scrollTop button. NOTE: You will have to visit the plugin settings page and manually save the options for both the frontend and the backend in order to activate the new options

## Version 0.7.0
### Changed
- Introduced namespaces
- Introduced automated testing
- Fixed a variety of bugs including not resetting options, not saving options and translation errors
- Enhanced performance
### Added
- Added minified versions of js and css files
- Added Dashicons
- Added custom scroll-to
- Added option to change the arrow color of the backtop arrow (NOTE: width and height must be 48px for it to be centered,  for now)
### Removed
- Removed Font Awesome Fonts
- Removed Fancy Select Library
- Removed Alertify Library
- Removed ScrollTo Library

## Version 0.6.7
### Changed
- Should have resolved that compatibility warning on this plugin's homepage

## Version 0.6.6
### Changed
- Should have resolved that compatibility warning on this plugin's homepage

## Version 0.6.5
### Changed
- Minor css improvement

## Version 0.6.4
### Changed
- Fixed scrollbar display issues
- Renamed the main plugin file from nsr.php to nicescrollr.php for testing reasons, so you may need to activate the plugin again

## Version 0.6.3
### Changed
- Minor code clean up
### Added
- Added Options to set border color and background color of the backtop button for hover state

## Version 0.6.2
### Changed
- Renamed css class "top-is-visible"  to "nsr-backtop-is-visible" due to compatibility reasons with buttons that are shipped with themes
- Renamed the plugin's js instances to avoid any possible conflicts with other js libraries
- Changed the element type for the backtop button, since no anchor is needed
- Changed the css for the backtop button to appear more neutral
- Changed some default option values
- The color picker accepts keyboard inputs now
- The color picker offers an alpha channel now
- Minor style changes
- Minor code changes

## Version 0.6.0
### Changed
- Moved the option "Scrollbar Default Look" down the list and renamed it to "Show Default Scrollbar"
- Hooked plugin initialisation to "plugins_loaded"
- Minor style changes
- Minor code changes
### Added
- Added an option to enable Nicescroll on mobile devices. default value is off

## Version 0.5.8
### Changed
- Checked compatibility with the latest version of WordPress

## Version 0.5.7
### Changed
- Changed default z-index value to 99998 (it is on top of everything except the admin bar)
- Changed default auto-hide mode for frontend and backend to 'disabled'
- Optimized the listener that watches changes on the document

## Version 0.5.6
### Changed
- Renamed the class name for the backtop button
- Fixed a bug regarding the scroll-to functionality, which is used to scroll to settings that did not pass Validation
- Several code improvements
- Updated the note regarding my support forum visits
- Moved the reset buttons to the respective settings tabs
### Added
- Added some styling Options for the backtop buttons. I advise you to navigate to the settings 
- Added credit to the author of Nicescroll in the description text
### Removed
- Removed the deprecated plugin settings tab

## Version 0.5.5
### Changed
- The plugin can handle document changes now (adopts to changes in height)

## Version 0.5.4
### Changed
- Fixed an error that occurred during plugin activation

## Version 0.5.3
### Changed
- Updated the Nicescroll library to the latest version (3.7.6), now loads from CDN with local fallback
- Updated jQuery Easing library to the latest version (1.4.1), now loads from CDN with local fallback
- Updated the settings and the default values to fit the current Nicescroll version
- Updated readme files and the help tab
- Back-top button is now available for the entire backend
- Minor stability and performance improvements
- Minor UI changes
### Added
- Added the optional back-top button for the entire frontend

## Version 0.5.2
### Changed
- Fixed a bug regarding the case of an empty Options array
- Minor code optimisations
- Minor UI changes
- Disabled Nicescroll Options that are not related to this plugins's functionality (boxzoom, iframeautozesize, ...)
### Removed
- Removed a plugin setting - ScrollTo is enabled by default now
- Removed local fallback for Font Awesome

## Version 0.5.0
### Changed
- Upgraded Nicescroll to version 3.6.8. Scroll behaviour may has changed since the library changed. Tweak the settings mentioned under 4. in this list if necessary
- Added easing
- Upgraded Alertify
- Replaced IcoMoon icons in favour of Font Awesome 4.7.0 ( loads via CDN with local fallback )
- Moved "Scroll Speed" and "Mouse Scroll Step" to the basic settings section for easier access
- Refactored and optimized scripts and code
- Updated the default settings
- Resolved an issue with "scroll top"

## Version 0.4.0
### Changed
- Minor bug fixes
- Minor code optimisations

## Version 0.3.0
### Changed
- Updated the user interface, all Options are available again
- Refactored the javascripts
- Support for PHP Version 5.3 ( was 5.4+ )
- Minor code optimizations
### Removed
- Removed "Fugaz One" font family

## Version 0.2.2
### Changed
- Nicescroll is now being disabled on mobile devices with a screen width below 960px to enhance user experience and prevent touch-related incompatibilities

## Version 0.2.1.1
### Changed
- Fixed compatibility with "cbParallax", which uses the Nicescroll library
### Added
- Fixed missing stuff...

## Version 0.2.1
### Changed
- Some minor code and comment cleanup
### Added
- Added an option to replace the nicescroll scrollbar with the browsers default scrollbar

## Version 0.2.0
### Changed
- Resolved the translation bugs
- Optimized the default settings for both the frontend and the backend
- Implemented the nicescroll.plus-library. It basically widens the rail and the cursor if their width is below 16px to 16px on hover so it's easier to grab it
- Modified the interface
- Updated the screenshots

## Version 0.1.1
### Changed
- Fixed the icons
- Replaced the switch for the checkbox with one that's compatible with Safari browsers
- Slightly modified the content of the help settings_tab
- Updated the German translation
- Fixed an issue with a missing url for "scrollTo"
- Made the Validation error notices dismissable
- The scrollTo-extension now also works for the color pickers
- Some minor bug fixes
### Added
- Added the option to assign a background color to the rail on both frontend and backend

## Version 0.1.0
First commit.