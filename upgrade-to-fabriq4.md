# Upgrade guide for the road to Fabriq 4.0

## Controllers

The following changes need to be made to controller files from Fabriq apps created before Fabriq 4.0:

- Controllers should now live in the namespace Fabriq\App\Controllers
- Controller class names should be renamed from snakecase (ex: homepage_controller) to camelcase with the first letter capitalized as proper class name (ex: HomepageController)
