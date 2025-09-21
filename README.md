# Mytheresa Promotions API

A high-performance REST API built with Symfony for managing product promotions and discounts. This application demonstrates enterprise-level Clean Architecture with proper Domain/Application/Infrastructure separation, caching, comprehensive testing, and Docker containerization.

## 🛠️ Technology Stack

- **PHP 8.2** with strict typing
- **Symfony 7.3** framework
- **Doctrine ORM** for database operations
- **ApiPlatform** for API documentation and standards
- **MySQL 8.0** for data persistence
- **Redis** for caching
- **PHPUnit** for testing
- **Docker & Docker Compose** for containerization

## 📋 Requirements

- Docker & Docker Compose
- Make (optional, for convenience commands)

## 🚀 Quick Start

### Using Make (Recommended)

```bash
# Complete setup with one command
make setup

# Or step by step:
make build    # Build containers
make up       # Start services
make install  # Install dependencies and setup database
```

## 📖 API Documentation

Once the application is running, visit:

- **Swagger UI**: http://localhost:8080/api/docs
- **API Endpoint**: http://localhost:8080/api/products

## 🔍 API Usage

### Get Products with Discounts

```bash
# Get all products (max 5)
curl "http://localhost:8080/api/products"

# Filter by category
curl "http://localhost:8080/api/products?category=boots"

# Filter by price (before discounts)
curl "http://localhost:8080/api/products?priceLessThan=80000"

# Combine filters
curl "http://localhost:8080/api/products?category=boots&priceLessThan=90000"
```

### Response Format

```json
[
  {
    "sku": "000001",
    "name": "BV Lean leather ankle boots",
    "category": "boots",
    "price": {
      "original": 89000,
      "final": 62300,
      "discount_percentage": "30%",
      "currency": "EUR"
    }
  }
]
```

## 🧪 Testing

```bash
# Run all tests
make test
```

## 🔧 Development Commands

```bash
# Access container shell
make shell

# View logs
make logs

# Restart services
make restart

# Stop services
make down

# Run database migrations
make migrate

# Seed database
make seed
```

## 🏗️ Clean Architecture Implementation

The application follows **Clean Architecture** principles with clear separation of concerns:

```
src/
├── Domain/             # Business Logic & Entities (Framework Independent)
│   ├── Entity/         # Core Business Entities
│   ├── ValueObject/    # Immutable Value Objects
│   └── Repository/     # Repository Interfaces (Contracts)
├── Application/        # Use Cases & Application Services
│   ├── Service/        # Application Services & Interfaces
│   └── DTO/            # Data Transfer Objects
├── Infrastructure/     # External Dependencies & Data Access
│   └── Repository/     # Concrete Repository Implementations
├── Controller/         # HTTP Layer (Presentation)
└── Command/            # Console Commands
```


## 🎯 Key Architectural Decisions & Rationale

### 1. **Clean Architecture Pattern**
**Decision**: Implemented Domain/Application/Infrastructure separation

**Rationale**:
- **Testability**: Business logic is isolated and easily testable without framework dependencies
- **Maintainability**: Changes in one layer don't affect others
- **Flexibility**: Easy to swap implementations (e.g., change from MySQL to PostgreSQL)
- **SOLID Principles**: Each layer has single responsibility and depends on abstractions

### 2. **Domain-Driven Design (DDD) Elements**
**Decision**: Used Entities, Value Objects, and Repository patterns

**Rationale**:
- **Product Entity**: Represents core business concept with behavior
- **Price Value Object**: Immutable object that encapsulates price logic and currency
- **Repository Interface**: Abstracts data access, allowing different implementations

### 3. **Dependency Inversion Principle**
**Decision**: All dependencies point inward toward the domain

**Rationale**:
```
Controller → Application Services → Domain Entities
     ↓              ↓                    ↑
Infrastructure ← Repository Interface ←──┘
```
- Domain layer has no external dependencies
- Application layer depends only on domain interfaces
- Infrastructure implements domain contracts

### 4. **Service Layer Pattern**
**Decision**: Separate services for different concerns (ProductService, DiscountService)

**Rationale**:
- **Single Responsibility**: Each service handles one business concern
- **Reusability**: Services can be used by different controllers or commands
- **Testability**: Easy to mock and unit test business logic

### 5. **Caching Strategy**
**Decision**: Redis caching with intelligent cache keys and 5-minute TTL

**Rationale**:
- **Performance**: Reduces database queries for frequently accessed data
- **Scalability**: Can handle high traffic with cached responses
- **Cache Invalidation**: Short TTL ensures data freshness
- **Key Strategy**: MD5 hash of filters ensures unique cache keys

### 6. **Database Design**
**Decision**: Strategic indexing on `category` and `price` columns

**Rationale**:
- **Query Performance**: Indexes on filtered columns improve query speed
- **Scalability**: Designed to handle 20,000+ products efficiently
- **Data Integrity**: Unique constraint on SKU prevents duplicates

### 7. **Discount Engine Design**
**Decision**: Rule-based discount system with collision handling

**Rationale**:
- **Extensibility**: Easy to add new discount rules
- **Business Logic**: "Bigger discount wins" rule clearly implemented
- **Separation**: Discount logic isolated in dedicated service

### 8. **API Design**
**Decision**: RESTful API with query parameter filtering

**Rationale**:
- **Standards Compliance**: Follows REST conventions
- **Flexibility**: Filters can be combined for complex queries
- **Performance**: Filtering at database level, not in application
- **Documentation**: OpenAPI/Swagger for clear API contracts

### 9. **Testing Strategy**
**Decision**: Comprehensive testing with Unit, and Functional tests

**Rationale**:
- **Unit Tests**: Test business logic in isolation with mocks
- **Functional Tests**: Test complete HTTP request/response cycle
- **Coverage**: Ensures all discount scenarios and edge cases are tested
- **Regression Protection**: Prevents breaking changes

### 10. **Value Objects Pattern**
**Decision**: Price as immutable value object with business methods

**Rationale**:
- **Immutability**: Prevents accidental price modifications
- **Encapsulation**: Price logic (currency, discount calculations) contained within
- **Type Safety**: Prevents primitive obsession with raw integers

### 11. **DTO Pattern**
**Decision**: Separate DTOs for API responses

**Rationale**:
- **API Stability**: Changes to entities don't break API contracts
- **Security**: Only expose necessary data to API consumers
- **Versioning**: Easy to create different API versions with different DTOs

### 12. **Interface Segregation**
**Decision**: Small, focused interfaces for each service

**Rationale**:
- **Testability**: Easy to create mocks for testing
- **Flexibility**: Can swap implementations without changing clients
- **Dependency Injection**: Clean service container configuration

## 🔄 **Data Flow Example**

```
1. HTTP Request → ProductController
2. Controller → ProductService (Application Layer)
3. ProductService → ProductRepository (via Interface)
4. Repository → Database Query
5. ProductService → DiscountService (Business Logic)
6. DiscountService → Price Value Object Creation
7. ProductService → ProductResponseDTO Creation
8. Controller → JSON Response
```

## 📈 Monitoring & Logging

- **Symfony Profiler**: Available in development mode
- **Structured Logging**: Using Monolog for proper log management
- **Health Checks**: Built-in health monitoring capabilities
