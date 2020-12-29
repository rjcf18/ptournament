# Pool Tournament App

## Index
* [Summary](#summary)
* [Setup](#setup)
* [Business Logic Description](#business-logic-description)
  * [Tournament Rules](#tournament-rules)
  * [Website Structure](#website-structure)
* [Project Tech Requirements](#project-tech-requirements)

## Summary
A proof of concept project to apply some concepts with as minimal framework interference as possible.

This project consists in a simple pool tournament application to manage and keep track of the tournament results.

## Setup

- In order to setup/run the project it is required to have PHP (any version should suffice) and Composer installed.
- Go to the root folder and run `composer setup` or `composer run setup`. The command will setup the docker environment with everything you need.
- In order to run the unit tests run `composer unitTest` or `composer run unitTest`
- For other commands check the composer.json file scripts section

After setting up the project, for communicating with the API try sending requests to `http://localhost` or any of the other endpoints configured in `config/routing.yml`

## Business Logic Description
Imagine you are organizing a pool **tournament** with some **friends**. A **friend** plays a **match** against another
**friend**, we need to be able to track the date of the match, the number of balls left for the looser, and the
winner.

### Tournament Rules
- 3 points for the winner, 1 for the looser and 0 for absences
- Friend A can only play once against friend B

### Website Structure
- Homepage
  - Block with ranking
    - Ordered by points (first) and fewer balls left (second)
  - Block with list of matches
    - Search by friend name
    - Link to match detail
    - Link to friend detail
- Match detail page
  - Match info
- Friend detail page
  - Friend info
  - Block with list of games
- Submission page
  - Form to submit a match result between two friends

## Project Tech Requirements
- Frameworks should be avoided (like Laravel for example)
- The data for the pages should be provided through a REST API
- Server Stack:
  - Apache / Nginx
  - PHP 7.X+
  - MySQL 5.7 
- Applicational Stack:
  - Composer
  - SQL Dump or some kind of migrations / seeds with dummy data
  - HTML5 / CSS3