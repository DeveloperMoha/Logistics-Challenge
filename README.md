# Logistics-Challenge
# 🚚 Logistics Challenge - Order Distribution Service

This repository contains a PHP service class that provides an optimized and scalable solution to a real-world logistics distribution problem.

## 📦 The Problem

You're running a logistics company that has:

- A number of **employees**, each with a home location (`latitude` & `longitude`)
- A list of **orders**, each with a delivery location (`latitude` & `longitude`)

### 🎯 Objective

Distribute the orders among employees based on the following rules:

1. ⚖️ **Fair Distribution**:  
   - All employees should receive an equal number of orders.
   - If the count is uneven, no employee should exceed another by more than one order.

2. 📍 **Proximity Assignment**:  
   - Orders should be assigned to the **nearest available employee**, starting from their **home location**.
   - For every next order assigned, use the **employee’s latest delivery location** to determine the closest available order.

## 🛠️ Features

- Fast computation using distance-based comparison
- Adaptive assignment using each employee’s most recent location
- Easy integration into any Laravel/Lumen/PHP project
- Can be extended to support time windows, priorities, or max-distance caps

## 🚀 Getting Started

To use this service:

1. Define your employees with their `homeLat` and `homeLong`.
2. Define your orders with their location points.
3. Pass them to the `handleOrders()` method and get the optimized assignment list.

---

### ✅ Example

```php
$service = new OrderDistributionService();
$assignments = $service->handleOrders($orders, $employees);
given example of 11 orders with existing 3 employees
