Mailing for TYPO3 CMS
=====================

Template based, render a variety of forms such as contact form, registration form, etc... effortless!

Consider these minimum steps to display a form and start submitting data:

* Create a content element of type "mailing" in the Backend.
* Add some TypoScript configuration to declare a new HTML template.
* Adjust your template in particular the form. Use a form generator of your choice.
* Your form is basically ready. 

Project info and releases
-------------------------

<!--Stable version:-->
<!--http://typo3.org/extensions/repository/view/mailing-->

Development version:
https://github.com/Ecodev/mailing

	git clone https://github.com/Ecodev/mailing.git

News about latest development are also announced on http://twitter.com/fudriot

Installation and requirement
============================

The extension **requires TYPO3 7 LTS**. Install the extension as normal in the Extension Manager from the TER (to be released) or download via Composer:

```

	"require": {
	    "fab/mailing": "dev-master",
	}

	-> next step, is to open the Extension Manager in the BE.
```

You are almost there! Create a Content Element of type "mailing" in `General Plugin` > `Mailing list` and configure at your convenience.

![](https://raw.githubusercontent.com/Ecodev/mailing/master/Documentation/Backend-01.png)

Configuration
=============

The plugin can be configured in TypoScript. Settings in the BE could override the TS value.


The Recipient list defined as Vidi selection - by