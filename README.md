## [Portfolio Version] Readme

https://nu.edu is a WordPress multisite that contains 7 subdomains (info.nu.edu, alumni.nu.edu, etc). I was tasked with creating a 'top-level' plugin that consolidated certain data and functionality that is currently replicated across each individual sub-site. Still somehwat of a work-in-progress, as most projects like this are.

1) Global Variables
Certain data content (# of worldwide alumni, # of degree programs, HQ Address & phone number, etc) needs to be consistent across all sites and instances. Created custom settings page at the 'network' level of the multisite to have these entries be the single-source-of-truth for all subsites. These variables can then be outputted to the site via shortcode. Output shortcodes were built to include certain modifiers to give us flexibility in how they are displayed on the front end.

2) Web Service Integrations
We use a number of third-party web service integrations that load on the front end of our site. These include Google Tag Manager, Optimizely, BrightEdge, and others. These were previously integrated for each individual sub-site. As a part of  this, I created a single "command center" settings page at the 'network' level of the multisite where we can manage & update each service's integration on a site-by-site basis. This is especially useful because many of these integrations are SEO-related, and this allows our (semi-non-technical) SEO team to make various adjustments on their own without requiring actual code updates.


## Screenshots
taken from local dev environment

![Screenshot 1](/screenshots/network-settings-1.png?raw=true "Custom Settings Page 1")
![Screenshot 2](/screenshots/network-settings-2.png?raw=true "Custom Settings Page 2")