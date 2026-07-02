# parabase

A PHP 8.1 web framework starter that's designed to be light and easy to build on, so you can "just get started".

---

## Requirements

- PHP 8.1+
- (Preferrably) MariaDB or MySQL,
- (Optionally) Redis,
- Apache with `mod_rewrite` enabled, or you can use nginx (you'd want to remove the .htaccess file though)
- [Composer](https://getcomposer.org/), for it's autoload functionality

---

## Installation

```bash
# 1. Clone the repo
git clone https://github.com/parakeet-live/parabase
cd parabase

# 2. Tweak composer.json to include directories that you may need an autoload from rather than *only* parabase
# If you're fine leaving it as-is, go for it. I chose nano because it's the easist to start with (imo, but i also dont know many text editors) and usually installed on most distributions of linux.
nano composer.json

# 3. Install PHP dependencies
composer install

# 4. Copy and fill in the config
cp private/cfg.example.php private/cfg.php
# Then edit private/cfg.php with your database credentials and site name

# 5. Do what you need with your database
```

Apache only: Point your virtual host's document root at the project root (the folder that contains `index.php`). Make sure `mod_rewrite` is on and `AllowOverride All` is set so `.htaccess` will properly protect your project files.

---

## Notice
I've added comments around the code to help you understand how to use the base, but this base is more of an "explore on your own" type thing.

I'm releasing this because one of my devs for my project like my base so much that they're spamming it now so I want to see if anyone else cares.

## Tip

Another thing you can do is move the private folder outside of the public folder that your webserver will serve to users.
Then you don't need .htaccess/nginx rules to protect it.
