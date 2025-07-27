# Migrate Recipes from Grocy to Mealie

This is a helper-script that helps you to move recipes from grocy to meali. 
**This is meant to run only once! **

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
