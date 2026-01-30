---
title: "TypeScript 最佳实践"
date: 2026-01-28
category: "技术"
tags: ["TypeScript", "前端", "最佳实践"]
---

# TypeScript 最佳实践

TypeScript 为 JavaScript 项目带来了类型安全，让我们来看看一些最佳实践。

## 1. 严格模式启用

始终在 `tsconfig.json` 中启用严格模式：

```json
{
  "compilerOptions": {
    "strict": true,
    "noUncheckedIndexedAccess": true,
    "noImplicitOverride": true
  }
}
```

## 2. 类型 vs 接口

知道何时使用类型别名，何时使用接口：

```typescript
// 使用接口定义对象形状
interface User {
  id: string
  name: string
  email: string
}

// 使用类型别名定义联合类型
type Status = 'pending' | 'approved' | 'rejected'

// 使用类型别名定义工具类型
type Nullable<T> = T | null
```

## 3. 泛型最佳实践

```typescript
// 使用泛型约束
interface Lengthwise {
  length: number
}

function logLength<T extends Lengthwise>(arg: T): void {
  console.log(arg.length)
}

// 使用泛型默认值
function identity<T = string>(value: T): T {
  return value
}
```

## 4. 类型守卫

```typescript
function isString(value: unknown): value is string {
  return typeof value === 'string'
}

function processValue(value: unknown) {
  if (isString(value)) {
    // TypeScript 知道这里 value 是 string
    console.log(value.toUpperCase())
  }
}
```

## 5. 工具类型

善用 TypeScript 内置工具类型：

```typescript
interface User {
  id: string
  name: string
  email: string
  password: string
}

// Pick - 选择部分属性
type PublicUser = Pick<User, 'id' | 'name'>

// Omit - 排除部分属性
type SafeUser = Omit<User, 'password'>

// Partial - 所有属性可选
type PartialUser = Partial<User>

// Required - 所有属性必需
type RequiredUser = Required<Partial<User>
```

## 总结

遵循这些最佳实践可以让你的 TypeScript 代码更加健壮和可维护。
