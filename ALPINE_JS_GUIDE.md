# Alpine.js Integration Guide

## Overview

Alpine.js is a rugged, minimal framework for composing JavaScript behavior in your Blade templates. It allows you to leverage the reactive and declarative nature of big frameworks like Vue or React at a much lower cost.

**Version**: 3.13.0+
**Size**: ~15KB gzipped
**Learning Curve**: Very gentle (2-3 hours to master)

## Why Alpine.js?

- ✅ Lightweight and performant
- ✅ No build step required (works directly in Blade)
- ✅ Reactive data binding
- ✅ Perfect for Laravel + Blade projects
- ✅ Progressive enhancement friendly
- ✅ Minimal JavaScript knowledge needed

## Installation & Setup

Alpine.js is already installed via npm. To use it:

1. **It's automatically loaded** in `resources/js/app.js`:
```javascript
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
```

2. **The main layout includes it** via Vite:
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

## Core Concepts

### 1. **Data (x-data)**
Initialize Alpine on an element and define its data/behavior:

```blade
<div x-data="{ open: false }">
    <!-- Data available here -->
</div>
```

### 2. **Text Interpolation (x-text)**
Display dynamic text:

```blade
<div x-data="{ count: 0 }">
    <span x-text="count"></span>
</div>
```

### 3. **Two-way Binding (x-model)**
Bind input values to reactive data:

```blade
<div x-data="{ name: '' }">
    <input x-model="name" type="text">
    <span x-text="'Hello ' + name"></span>
</div>
```

### 4. **Event Listeners (@click, @submit, etc.)**
Listen to DOM events:

```blade
<div x-data="{ count: 0 }">
    <button @click="count++">Increment</button>
    <span x-text="count"></span>
</div>
```

### 5. **Conditional Rendering (x-show, x-if)**
Show/hide or mount/unmount elements:

```blade
<div x-data="{ open: false }">
    <!-- x-show: toggles display property (stays in DOM) -->
    <div x-show="open">Shown when true</div>
    
    <!-- x-if: adds/removes from DOM entirely -->
    <div x-if="open">Removed from DOM when false</div>
    
    <button @click="open = !open">Toggle</button>
</div>
```

### 6. **List Rendering (x-for)**
Render lists:

```blade
<div x-data="{ items: ['Apple', 'Banana', 'Orange'] }">
    <ul>
        <template x-for="item in items" :key="item">
            <li x-text="item"></li>
        </template>
    </ul>
</div>
```

### 7. **Attributes (: prefix or x-bind)**
Bind attributes dynamically:

```blade
<div x-data="{ link: 'https://example.com' }">
    <!-- These are equivalent -->
    <a :href="link">Link 1</a>
    <a x-bind:href="link">Link 2</a>
</div>
```

### 8. **Classes (: or x-bind:class)**
Toggle classes conditionally:

```blade
<div x-data="{ active: false }">
    <!-- Object syntax -->
    <div :class="{ 'bg-blue-500': active, 'bg-gray-500': !active }">
        Box
    </div>
    
    <!-- String syntax -->
    <div :class="active ? 'text-green-600' : 'text-red-600'">
        Text
    </div>
    
    <button @click="active = !active">Toggle</button>
</div>
```

## Examples in JobSeekers App

### Example 1: Navigation Menu Toggle (Already Implemented)
```blade
<nav x-data="{ mobileOpen: false, userMenuOpen: false }">
    <button @click="mobileOpen = !mobileOpen">Menu</button>
    
    <div x-show="mobileOpen">
        <!-- Mobile menu content -->
    </div>
    
    <div @click.outside="userMenuOpen = false">
        <!-- Dropdown menu that closes when clicking outside -->
    </div>
</nav>
```

### Example 2: Password Visibility Toggle (Already Implemented)
```blade
<div x-data="{ showPassword: false }">
    <input :type="showPassword ? 'text' : 'password'" name="password">
    <button @click="showPassword = !showPassword">
        <svg x-show="!showPassword">Eye Icon</svg>
        <svg x-show="showPassword">Eye Closed Icon</svg>
    </button>
</div>
```

### Example 3: Form Validation
```blade
<div x-data="{ email: '', isValid: false }">
    <input 
        x-model="email" 
        @input="isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)"
        type="email"
    >
    <span x-show="!isValid && email" class="text-red-600">Invalid email</span>
    <span x-show="isValid" class="text-green-600">✓ Valid</span>
</div>
```

### Example 4: Tabs/Accordion
```blade
<div x-data="{ activeTab: 'tab1' }">
    <div class="flex gap-4">
        <button 
            @click="activeTab = 'tab1'"
            :class="{ 'border-blue-600': activeTab === 'tab1' }"
        >
            Tab 1
        </button>
        <button 
            @click="activeTab = 'tab2'"
            :class="{ 'border-blue-600': activeTab === 'tab2' }"
        >
            Tab 2
        </button>
    </div>
    
    <div x-show="activeTab === 'tab1'">Content 1</div>
    <div x-show="activeTab === 'tab2'">Content 2</div>
</div>
```

### Example 5: Search with Filtering
```blade
<div x-data="{ query: '', results: [] }">
    <input 
        x-model="query" 
        @input="query.length > 2 ? fetchResults() : results = []"
        placeholder="Search jobs..."
    >
    
    <div x-show="results.length > 0">
        <template x-for="result in results" :key="result.id">
            <div x-text="result.title"></div>
        </template>
    </div>
    
    <script>
        function fetchResults() {
            // Simulate API call
            this.results = [
                { id: 1, title: 'Match 1' },
                { id: 2, title: 'Match 2' }
            ];
        }
    </script>
</div>
```

## Directives Reference

| Directive | Purpose |
|-----------|---------|
| `x-data` | Initialize Alpine component |
| `x-show` | Toggle display property |
| `x-if` | Add/remove from DOM |
| `x-text` | Set text content |
| `x-html` | Set HTML content (use cautiously) |
| `x-model` | Two-way data binding |
| `@click` | Listen to click event |
| `@submit` | Listen to form submit |
| `@change` | Listen to change event |
| `@input` | Listen to input event |
| `:class` | Bind class attribute |
| `:style` | Bind style attribute |
| `:disabled` | Bind disabled attribute |
| `:href` | Bind href attribute |
| `x-for` | Render list |
| `x-transition` | Apply transitions |
| `x-cloak` | Hide until Alpine initializes |
| `@click.outside` | Detect click outside element |
| `@click.prevent` | Prevent default action |
| `@submit.prevent` | Prevent form submission |

## Advanced Patterns

### Using Methods
```blade
<div x-data="{ 
    items: [],
    addItem(name) {
        this.items.push({ id: Date.now(), name });
    },
    removeItem(id) {
        this.items = this.items.filter(item => item.id !== id);
    }
}">
    <input x-model="newItem" @keydown.enter="addItem(newItem); newItem = ''">
    <template x-for="item in items" :key="item.id">
        <div class="flex justify-between">
            <span x-text="item.name"></span>
            <button @click="removeItem(item.id)">Delete</button>
        </div>
    </template>
</div>
```

### Computed Properties
```blade
<div x-data="{
    price: 100,
    quantity: 1,
    get total() {
        return this.price * this.quantity;
    }
}">
    <input x-model.number="quantity" type="number">
    <span x-text="'Total: $' + total"></span>
</div>
```

### Watchers (Using Effect)
```blade
<div x-data="{ count: 0 }">
    <button @click="count++">Increment</button>
    <span x-text="count"></span>
    
    <script>
        // This will run whenever count changes
        Alpine.effect(() => {
            console.log('Count is now:', Alpine.store('data').count);
        });
    </script>
</div>
```

### Global State with Alpine.store()
```blade
<!-- Define global store once -->
<script>
    Alpine.store('app', {
        theme: 'light',
        toggleTheme() {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
        }
    });
</script>

<!-- Use in any component -->
<div x-data>
    <button @click="$store.app.toggleTheme()">
        Theme: <span x-text="$store.app.theme"></span>
    </button>
</div>
```

## Performance Tips

1. **Use x-show for frequent toggles** (keeps in DOM)
2. **Use x-if for content rarely shown** (removes from DOM)
3. **Avoid heavy computations in x-text** (compute once in methods)
4. **Use :key in x-for** (helps Alpine track items)
5. **Debounce search/input events** for API calls

```blade
<!-- Good - debounced search -->
<input 
    @input="$debounce(() => searchJobs(query), 500)"
    x-model="query"
>
```

## Integration with Laravel Features

### CSRF Protection
Alpine.js automatically respects CSRF tokens in forms:

```blade
<form @submit="submitForm">
    @csrf
    <input x-model="name" name="name">
    <!-- CSRF token is automatically included -->
</form>
```

### Route Helpers
Use Laravel route helpers in Alpine:

```blade
<div x-data="{
    loginUrl: '{{ route("login") }}',
    dashboardUrl: '{{ route("dashboard") }}'
}">
    <a :href="loginUrl">Login</a>
</div>
```

### Passing PHP Data
```blade
<div x-data="{
    user: {{ Illuminate\Support\Js::from(auth()->user()) }},
    jobs: {{ Illuminate\Support\Js::from($jobs) }}
}">
    <span x-text="user.name"></span>
</div>
```

## Debugging

### Console Logging
```blade
<div x-data="{ count: 0 }">
    <button @click="count++; console.log('Count:', count)">Click</button>
</div>
```

### Alpine DevTools
Install Alpine DevTools browser extension for debugging:
- Chrome: Alpine DevTools extension
- Firefox: Alpine DevTools extension

### X-cloak (Hide Uninitialized Content)
```blade
<style>
    [x-cloak] { display: none; }
</style>

<div x-cloak x-data="{ count: 0 }">
    <!-- Hidden until Alpine initializes -->
</div>
```

## Browser Compatibility

Alpine.js v3 supports:
- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers
- ⚠️ IE11 (not supported, gracefully degrades)

## Best Practices

1. **Keep components small** - one directive per element when possible
2. **Use descriptive data names** - `x-data="{ isMenuOpen: false }"` not `{ a: false }`
3. **Prefer x-show over x-if** for UI toggles
4. **Use server-side for complex state** - keep data in session/database
5. **Leverage Laravel's reactivity** - reload page or use Alpine for minor updates
6. **Test Alpine components** - unit test your data/methods

## Common Pitfalls

❌ **Don't**: Mix complex business logic in x-data
✅ **Do**: Keep Alpine for UI interactions, use Laravel for business logic

❌ **Don't**: Use Alpine for real-time features
✅ **Do**: Use Laravel Echo + Broadcasting for real-time

❌ **Don't**: Store sensitive data in Alpine (visible in HTML)
✅ **Do**: Store sensitive data server-side in sessions/database

## Resources

- **Alpine.js Docs**: https://alpinejs.dev
- **Alpine.js GitHub**: https://github.com/alpinejs/alpine
- **Alpine UI Components**: https://alpine-ui.dev
- **Blade + Alpine Tutorial**: https://laravelnoobs.com/laravel-alpine-js-guide

## Next Steps

1. Explore Alpine.js documentation
2. Add more interactive features to the dashboard
3. Create reusable Alpine components
4. Consider Alpine.js libraries (Spruce for state management)
5. Profile performance and optimize as needed

---

**Integration Status**: ✅ Complete and Ready to Use
**Last Updated**: August 18, 2026
