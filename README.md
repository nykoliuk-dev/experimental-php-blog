# Experimental PHP Blog

An experimental blog application built with pure PHP to explore layered
architecture, domain-driven design principles, and testable code without
relying on a full-stack framework.

This project focuses on architecture and code organization rather than
UI or production readiness.

------------------------------------------------------------------------

## 🚀 Purpose

The goal of this project was to design a maintainable and testable
application using:

-   Layered architecture
-   Repository pattern
-   Service layer
-   Value Objects
-   Dependency inversion
-   Transaction abstraction
-   Unit testing

Instead of using a framework, the core architectural patterns were
implemented manually to better understand how modern PHP frameworks
operate internally.

------------------------------------------------------------------------

## 🏗 Architecture Overview

The project follows a layered structure:

### Controller Layer

Handles HTTP interaction and delegates business logic to services.

### Service Layer

Contains business logic and coordinates repositories and domain
operations.

### Repository Layer

Encapsulates all database access logic and maps database rows to domain
objects.

### Domain Layer

Includes: - Entities (Post, Comment, etc.) - Value Objects (PostId,
UserId, CategoryId, TagId, Pagination)

------------------------------------------------------------------------

## 🧠 Key Design Decisions

### Value Objects

Identifiers and domain concepts are wrapped in dedicated value objects
instead of using raw integers or arrays.\
This improves type safety and domain clarity.

### Repository Pattern

All persistence logic is abstracted behind interfaces, making the domain
independent from infrastructure details.

### Transaction Abstraction

Database transactions are handled through a
`TransactionManagerInterface`, allowing business logic to remain
framework-agnostic and testable.

### Facade Pattern

A `PostFacade` aggregates related data (post, tags, categories,
comments) to simplify controller logic.

------------------------------------------------------------------------

## 🧪 Testing

The project includes unit tests for:

-   Repository layer (using SQLite in-memory database)
-   PostService business logic
-   Slug generation logic
-   Category and tag assignment
-   Transaction handling

The goal was to ensure that business logic is isolated and fully
testable.

------------------------------------------------------------------------

## 📌 Features

-   Post creation
-   Category and tag assignment (many-to-many)
-   Comment pagination
-   Slug generation with transliteration support
-   Image upload handling
-   Server-side validation
-   JSON response handling for async requests

------------------------------------------------------------------------

## 🛠 Technologies

-   PHP 8+
-   PDO
-   SQLite (for testing)
-   PHPUnit

No framework was used intentionally.

------------------------------------------------------------------------

## 📖 What This Project Demonstrates

-   Understanding of SOLID principles
-   Manual implementation of layered architecture
-   Separation of concerns
-   Testable and maintainable code structure
-   Clean domain modeling without framework magic
