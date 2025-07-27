# Migrate Recipes from Grocy to Mealie

This is a helper-script that helps you to move recipes from grocy to meali. 
It will get all your recipes from grocy and import them via the JSON Import to Mealie. This will not migrate your pictures. 
All List-Items in the Description of a grocy recipe will be a instruction-step in Mealie.

> [!CAUTION]
> This is meant to run only once! It will always just import all recipes! No douplicate-checks. 

> [!IMPORTANT]  
> Create a Mealie Backup before running this. 

## Instructions

### Create .env 
- copy env.example to .env
- put in your data

### Build Docker Container

From the root of this repository run: 

```
docker build . -t gtm
```

### Run Docker Container

From the root of this repository run:

```
docker run  --env-file ~/Development/Docker/Grocy-to-Mealie/.env --rm --name GrocyMealie gtm
```
