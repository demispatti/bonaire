

<p align="center">
  <a href="https://wordpress.org/plugins/bonaire/" target="_blank">
    <img src="https://ps.w.org/bonaire/assets/icon-256x256.png" alt="Bonaire Logo" width="128" height="128">
  </a>
</p>

<h2 align="center">Bonaire</h2>
<p align="center">
A WordPress plugin to send replies via your website to messages you received with 
<a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Contact Form 7</a> and which you store with <a href="https://wordpress.org/plugins/flamingo/" target="_blank">Flamingo</a>.
<br><br>
<a href="https://wordpress.org/plugins/bonaire/" target="_blank"><strong>WordPress Plugin Repository Page</strong></a><br>
<a href="https://downloads.wordpress.org/plugin/bonaire.zip" target="_blank"><strong>Download</strong></a>
  <br>
  <br>
  <a href="https://github.com/demispatti/bonaire/issues/new?template=bug.md" target="_blank">Report A Bug</a><br>
  <a href="https://github.com/demispatti/bonaire/issues/new?template=feature.md&labels=feature" target="_blank">Request Feature</a>
</p>

---
## Table Of Contents
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Documentation](#documentation)
- [Frequently Asked Questions](#documentation)
- [Plugin Support](#plugin-support)
- [Bugs And Feature Requests](#bugs-and-feature-requests)
- [Contributing](#contributing)
- [Versioning](#versioning)
- [Creator](#creator)
- [Copyright And License](#copyright-and-license)

---
## Features
- Send replies to messages received trough a default "Contact Form 7" contact form
- Store replies on your mail server's 'Sent Items' folder  
- Dashboard Widget lists incoming messages  
- Email replies are text-only

---
## Requirements
- Familiarity with configuration and usage of both Contact Form 7 and Flamingo
- [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) installed and activated
- [Flamingo](https://wordpress.org/plugins/flamingo/) installed and activated
- PHP IMAP extension installed and enabled on your web server
- PHP 5.6+

---
## Installation
1. Upload the `bonaire` folder to your `/wp-content/plugins/` directory.
2. Activate the "Bonaire" plugin through the "Plugins" menu in WordPress.
3. You will find its settings page listed in the "settings" section.

---
## Quick Start
Head over to the settings page and enter your email account settings to get started.

---
## Documentation
Once you've installed the plugin, you'll find help tabs inside the WordPress contextual help system.

---
## Frequently Asked Questions
#### Where do I interact with this plugin and how does it work?
Please visit the plugin help tab for further information.

#### Why doesn't it show up in the settings menu after installing and activating?
Most likely, this is because you didn't install and activate the plugins Bonaire was made for, namely [Contact Form 7](https://wordpress.org/plugins/contact-form-7/)  and [Flamingo](https://wordpress.org/plugins/flamingo/).
After installing and activating these two plugins, Bonaire will show up on in the settings menu.

#### I have the two plugins installed and activated but it doesn't work!
That's unlikely.
First of all, you have to enter the email account settings for the email account that you use on the contact form. You can do that on the settings page "Settings -> Bonaire".
If you want to store your replies in your mail server's "Sent Items" folder, you have to enter your IMAP settings, too. Run the tests and you should be ready to go.

#### Are there any known limitations?
- Handling attachmants is not supported
- Simple plain text email format for now
- Supports one contact form for now

#### Are there any known issues?
No.

---
<!--## Plugin Support
If you need support or have a question, I check the WordPress plugin support section on [WordPress Plugin Repository](https://wordpress.org/support/plugin/bonaire/) once or twice a month.-->

---
## Bugs And Feature Requests
Have a bug or a feature request? Please first read the [issue guidelines](https://github.com/demispatti/bonaire/blob/master/.github/CONTRIBUTING.md#using-the-issue-tracker) and search for existing and closed issues. If your problem or idea is not addressed yet, [please open a new issue](https://github.com/demispatti/bonaire/issues/new).

---
## Contributing
Please read through our [contributing guidelines](https://github.com/demispatti/bonaire/blob/master/.github/CONTRIBUTING.md). Included are directions for opening issues, coding standards, and notes on development.

Moreover, if your pull request contains JavaScript patches or features, you must include [relevant unit tests](https://github.com/demispatti/bonaire/tree/master/js/tests). All HTML and CSS should conform to the [Code Guide](https://github.com/demispatti/code-guide), maintained by [Demis Patti](https://github.com/demispatti).

Editor preferences are available in the [editor config](https://github.com/demispatti/bonaire/blob/master/.editorconfig) for easy use in common text editors. Read more and download plugins at <https://editorconfig.org/>.

---
## Versioning
For transparency into our release cycle and in striving to maintain backward compatibility, Bonaire is maintained under [the Semantic Versioning guidelines](https://semver.org/). Sometimes we screw up, but we adhere to those rules whenever possible.

See [the Releases section of our GitHub project](https://github.com/demispatti/bonaire/releases) for changelogs for each release version of Nicescroll.

---
## Creator
**Demis Patti**
<https://github.com/demispatti>

---
## Copyright and license
Code and documentation copyright 2019 [Demis Patti](https://github.com/demispatti/bonaire/graphs/contributors). Code released under the [GPL V2 License](https://github.com/demispatti/bonaire/blob/master/LICENSE).
