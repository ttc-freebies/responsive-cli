# responsive-cli

A simple Console plugin that could run the source set thumbnails generation on the background.

### How to...

- Install the plugin and make sure the Responsive plugin is already installed and enabled (configured).
- Enable the plugin from the System->Plugins, using the filters to select the type: Console.
- Using SSH or a cron job run `php www/cli/joomla.php responsive:build`, assuming that your public folder is named `www` (if not adjust the command to reflect the correct path).
- That's it, observe PHP doing a recursive generation of different sizes of each image in all the folders specified in the plugin Filesystem::Local!

