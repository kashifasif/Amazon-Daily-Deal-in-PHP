# Download Amazon Daily Deal Product Information 
A executable scripts in PHP to download Amazon Daily Deal Product Information 


# How to Config it for cron job
If you want to make a php script executable, e.g. by typing ./script-name.php, you’ll want to add the following line to the top of the document:

#!/usr/bin/env php####
Yes, that goes before the <?php statement. You’ll also want to let your *nix environment know that the file is executable. You can do this by typing:

chmod u+x filename.php
One of the cool things about *nix land is that file extensions don’t matter, so you can rename the file and leave off the .php part. Then your script will work by typing ./script-name.

But, we can do better. Perhaps you don’t want to type the ./ part either. To fix this, you need to add the script to your PATH. Your PATH is a set of directories which your shell environment looks in to find executable files.

To see what your path is, run:

echo $PATH
Which, on my system, tells me /usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:/usr/local/bin:/usr/X11/bin (since I’m in OS X).

What you can do is make a bin (or hidden .bin) directory in your home and place your various command line scripts in there, then add that directory to your path. You can do all of these tasks by running the following commands:

echo "PATH=\$HOME/bin:\$PATH" >> ~/.bash_profile
source ~/.bash_profile
From now on, any scripts you have in your ~/bin directory will be executed with a higher priority than existing scripts (if there is a name conflict). So, you can now run your PHP script by typing “script-name” from anywhere.

Bonus: If you are in OS X, and use Finder, and don’t want that ugly bin directory showing up all the time, type the following:

chflags hidden ~/bin
