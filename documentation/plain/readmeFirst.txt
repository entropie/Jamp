Attention:
  I will not continue this version of Jamp.
	The first and important fact is I dont like
	PHP anymore. Uh, of course it's the script language which leaded me
	to programming itself, but PHP is ... I don't know...
	Secondly, ruby. Nothing more.

	(PHP.)Jamp is a working piece of code. Ok, its not the most finest,
	but it does its work. (My music database: ~8500 files in 60 GB atm.)
	I used the PHP version of Jamp for about 3 years (from the first
	working Version which has never been published (thanks god!))

	Actual, I will fix bugs in this version, new features will not come.
	Maybe, this fact will lead this version of Jamp coming up to a final
	`Jamp-1.0.0`

	I love Jamp, and I love PHP.
	I love all the OO developer, and all the people who makes things going
	the right way, the true way.
	Love, live and if you want, smoke!
/Attention


Moin,

This is a little webinterfaced mp3-streamer called Jamp.

## Requirements:
 * To read the documentation you have to understand my english.
 * Apache [ tested with 1.3.27, 2.0.44, 2.0.47 ].
    <http://www.apache.org/>
    Try it with another webserver and mail me the result.
 * PHP4 [ tested with 4.1, 4.3 ]
    <http://www.php.net/>
    It is important that 'short_open_tag' is turned 'On'
    in php.ini (default value).
 * MySxQL [ tested with 3.23.56, 4.0.13, 4.0.14-log ]
    <http://www.mysql.com/>
 * An Unix based OS
    Tested with Linux (Slackware 8.0/9.1, Knoppix, GreenFrog, Gentoo)

   You can download all requirements for free!

** You also should have:
 * WinAmp [ no matter which version ] or XMMS under Linux
    <http://www.winamp.com>
    You don't need WinAmp (works with M$ Media Player (BOWH!), too)
    but it's really better.

** Nice to have:
 * Mp3Blaster

   You can also download all "should have's" for free!

## Installation:
 * The first step is to edit the </cfg/cfg.php>, all variables are
    explained.
    Don't forget to set up the mysql settings
 * Upload (ASCII) all to your webspace in a dir like 'Jamp'
 * The webserver must have writing rights to the 'root' folder.
		Run the script <./scripts/chmod.php> in the command line.
 * Don't create any folders setted in <./cfg/cfg.php>,
    the script will do this job!
 * Run <http://[www].your-domain.tld/Jamp/setup/>
    If you get no errors, it should work :)
 * If all works fine, it´s recommended to delete the setup dir

## ThanX 2:
 * my mom & my mellifluously sister
 * ligi, for the introduction in Linux (the student beats
    the masta, anytime! ;)) & programing
 * my friends
 * All the open source freaks!

(EOF)
