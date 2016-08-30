Mailing for TYPO3 CMS
=====================

Authenticated FE users can send bunch of messages to a list of recipients. This list is defined as a dynamic selection in a FE module ([Vidi](https://github.com/fabarea/vidi)).

When the user hits the sending button, Mailing is preparing and delegating
the messages to [Messenger](https://github.com/fabarea/messenger) - which is a dependency. Messenger has a queue system to properly send mass emails and monitor the queue / sent emails. For that purpose, a scheduler task must be set up in the Scheduler module.
For a small number of recipients, emails can be sent directly and by pass the queue.
All that is configurable in the plugin settings in the BE.

![](https://raw.githubusercontent.com/Ecodev/mailing/master/Documentation/Frontend-01.png)

Project info and releases
-------------------------

Stable version:
http://typo3.org/extensions/repository/view/mailing

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

The Recipient list is defined in the FE module, powered by Vidi.