# Daylog

A journaling application built with clean architecture principles, following TDD from day one.

## Motivation

This project was started as an experiment:

- to see whether it is possible to learn something new with the help of ChatGPT;
- to build something actually working, more complex than just a “Hello, World”;
- to explore how much freedom can be given to the model;
- to find out what happens if ChatGPT is used as a real development assistant;
- to check whether my own programming skills would atrophy in the process.

As a result, Daylog turned into more than just a learning sandbox.  
It became a reference example of applying Clean Architecture, TDD,  
and rigorous documentation practices in a PHP project.

## Credits

Daylog was created in 2025 as a clean architecture / TDD learning project.  
It was developed collaboratively with the help of ChatGPT (OpenAI).

## Stack
- PHP 8.1+
- Fat-Free Framework (F3) in Infrastructure (DB layer), and in Presentation
- Composer, Codeception (unit/integration/functional)
- Vite for frontend (later)

## Required PHP Extensions

- mbstring

## Principles
- Framework-independent Domain/Application layers.
- One TDD step = one commit (Conventional Commits).
- English for code, tests, commits, and documentation.

- See `/docs` for charter, requirements, and use cases.
- See `/docs/CONFIGURATION.md` for environment and DB setup.  
- See `/docs/TESTING.md` for testing conventions and running suites.
- See `/docs/ARCHITECTURE_ERD_AND_GLOSSARY.md` for architecture overview (ERD + glossary).

## License

Daylog is licensed under the GNU General Public License v3.0.  
See the [LICENSE](LICENSE) file for details.
