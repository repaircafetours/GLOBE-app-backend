---
sidebar_label: Intended features
sidebar_position: 2
---

:::note

This synthetic page is directely for the attention of the next maintainer team in order to help them understand this project

:::

# What is it ?

This subproject is a part of a wider project able to manage reparation events for repair café separated in 3 parts : 

- A Wordpress plugin.
- A HumHub plugin
- The main application

This subproject corresponds to the backend of the main application which is a REST only API written in PHP 8.4 with the laravel framework, 
the front end is not handled by php nor laravel but with react in another repository.

# Personas

### Visitors

Possibly no IT knowledge and irregular users. Visitors may only access and update their own data.

:::note

UI for Visitors should be as simple as possible

:::

### Volunteers

Regular volunteers and users with a low level of access given with the HumHub plugin. 
However, they may not be comfortable with computers.

:::note

UI for Volunteers should be simple if possible

:::

### Operationnal volunteers

Regular users which are comfortable with computers. These are the users of the main application

### Intendants

Regular users with a general low level of access but are the only ones able to view critical personal data such as diet and allergies.

### Admins

Users which are comfortable with computers. May access any type of data ***without*** any exception (including personal data).


# Features

This section only lists features that are not already implemented in the API

## Adding a visitor to an event

Given a specific visitor, adds it to the next event and store in database moreover sending a confirmation email.

## Assigning an appointement between a visitor and volunteers 

Given a visitor and a volunteer, updates the event in database to store the appointement. Also send a confirmation email to the visitor
with the following information :

- Date and time of the appointment
- An access plan
- Safety information regarding their object
- Specific instructions regaarding their object

:::warning

The visitor must be registered to the corresponding event in order to assign them an appointement

:::

## Intervention of a volunteer

Given a volunteer and an object, sets the reparation status of the object. The volunteer may add more information like the root cause and comments.

:::note

Multiple volunteer can intervene on a single object.

:::

## Logging

Given a database update, logs the affected columns and the user which updated the corresponding object in database.

:::note

The user logged is either a volunteer or null with null corresponding to a visitor auto-updating themselves

:::

## Update limitation

Given a feature, change the deadline of allowed modification. For example, you may need to change the deadline of self-updates of visitors from 14 days (defalt) to 21 days before the event

:::note

This change can only be done by an administrator

:::

## Security

Using [Keycloak](https://www.keycloak.org/) secure the different endpoints using the same authentification which will be the HumHub login by default.

:::info

You may need to reach out the contracting authority of the project for more information

:::

## Intendants view

A special view of the data with only personal data of volunteers.

:::note

Intended for intendants but administrators by reach them too.

:::