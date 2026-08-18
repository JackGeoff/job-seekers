# Component Usage Guide

## Buttons

### Primary Button
```blade
<a href="#" class="btn btn-primary">
    Click me
</a>
```

### Secondary Button
```blade
<button class="btn btn-secondary">
    Secondary Action
</button>
```

### Ghost Button
```blade
<a href="#" class="btn btn-ghost">
    Subtle Action
</a>
```

### Button Sizes
```blade
<!-- Small -->
<button class="btn btn-primary btn-sm">Small</button>

<!-- Default -->
<button class="btn btn-primary">Default</button>

<!-- Large -->
<button class="btn btn-primary btn-lg">Large</button>
```

## Form Elements

### Basic Input
```blade
<div class="form-group">
    <label for="email" class="label">Email Address</label>
    <input 
        type="email" 
        id="email" 
        name="email"
        class="input"
        placeholder="you@example.com"
    >
</div>
```

### Input with Error
```blade
<div class="form-group">
    <label for="email" class="label">Email Address</label>
    <input 
        type="email" 
        id="email" 
        name="email"
        class="input ring-2 ring-error-500 border-error-500"
        placeholder="you@example.com"
    >
    <p class="mt-2 text-sm text-error-600">Invalid email address</p>
</div>
```

### Checkbox
```blade
<div class="flex items-center">
    <input
        type="checkbox"
        id="remember"
        name="remember"
        class="w-4 h-4 rounded border-neutral-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
    >
    <label for="remember" class="ml-2 text-sm text-neutral-600 cursor-pointer">
        Remember me
    </label>
</div>
```

### Select Input
```blade
<div class="form-group">
    <label for="role" class="label">Role</label>
    <select id="role" name="role" class="input">
        <option>Select a role</option>
        <option>Developer</option>
        <option>Designer</option>
    </select>
</div>
```

## Cards

### Basic Card
```blade
<div class="card">
    <h3 class="text-lg font-semibold text-neutral-900 mb-2">Card Title</h3>
    <p class="text-neutral-600">Card content goes here.</p>
</div>
```

### Card with Hover
```blade
<a href="#" class="card-hover">
    <h3 class="text-lg font-semibold text-neutral-900 mb-2">Clickable Card</h3>
    <p class="text-neutral-600">This card has hover effects.</p>
</a>
```

### Card with Image
```blade
<div class="card">
    <div class="w-full h-48 bg-neutral-200 rounded-lg mb-4"></div>
    <h3 class="text-lg font-semibold text-neutral-900 mb-2">Job Title</h3>
    <p class="text-neutral-600">Company Name</p>
</div>
```

## Typography

### Headings
```blade
<!-- Page Title -->
<h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-neutral-900">
    Main Heading
</h1>

<!-- Section Title -->
<h2 class="text-2xl font-bold text-neutral-900">
    Section Title
</h2>

<!-- Subsection -->
<h3 class="text-lg font-semibold text-neutral-900">
    Subsection
</h3>
```

### Body Text
```blade
<!-- Regular text -->
<p class="text-base text-neutral-900">Regular paragraph text</p>

<!-- Muted text -->
<p class="text-muted">Secondary text information</p>

<!-- Emphasis -->
<p class="text-emphasis">Important information</p>

<!-- Small text -->
<p class="text-sm text-neutral-600">Small secondary text</p>
```

## Badges & Labels

### Color Badges
```blade
<span class="inline-block px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-medium">
    Badge
</span>

<span class="inline-block px-3 py-1 bg-success-100 text-success-700 rounded-full text-xs font-medium">
    Success
</span>

<span class="inline-block px-3 py-1 bg-error-100 text-error-700 rounded-full text-xs font-medium">
    Error
</span>
```

## Layout Components

### Container
```blade
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Content -->
</div>
```

### Grid
```blade
<!-- 2 Column -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="card">Column 1</div>
    <div class="card">Column 2</div>
</div>

<!-- 3 Column -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Cards -->
</div>
```

### Section Spacing
```blade
<!-- Padding section -->
<section class="px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-24">
    <div class="max-w-7xl mx-auto">
        <!-- Content -->
    </div>
</section>
```

### Divider
```blade
<div class="divider"></div>
```

## Navigation

### Sticky Header
```blade
<nav class="border-b border-neutral-200 bg-white sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Content -->
        </div>
    </div>
</nav>
```

## States & Animations

### Loading State
```blade
<button class="btn btn-primary animate-pulse-subtle" disabled>
    Loading...
</button>
```

### Fade In Animation
```blade
<div class="animate-fade-in">
    This content fades in smoothly
</div>
```

### Slide Up Animation
```blade
<div class="animate-slide-up">
    This content slides up smoothly
</div>
```

## Utility Classes

### Text Utilities
```blade
<!-- Text colors -->
<p class="text-neutral-900">Dark text</p>
<p class="text-neutral-600">Secondary text</p>
<p class="text-primary-600">Primary colored text</p>

<!-- Text alignment -->
<p class="text-center">Centered</p>
<p class="text-right">Right aligned</p>

<!-- Font weight -->
<p class="font-medium">Medium weight</p>
<p class="font-semibold">Semibold weight</p>
<p class="font-bold">Bold weight</p>
```

### Spacing Utilities
```blade
<!-- Margin -->
<div class="mb-4">Margin bottom</div>
<div class="mt-6">Margin top</div>
<div class="px-4">Padding horizontal</div>
<div class="py-8">Padding vertical</div>

<!-- Gaps (for flexbox/grid) -->
<div class="flex gap-4">Items with gap</div>
<div class="grid gap-6">Grid with gap</div>
```

### Border & Shadow Utilities
```blade
<!-- Borders -->
<div class="border border-neutral-200">Light border</div>
<div class="border-t border-neutral-200">Top border only</div>
<div class="rounded-lg">Rounded corners</div>

<!-- Shadows -->
<div class="shadow-sm">Subtle shadow</div>
<div class="shadow-md">Medium shadow</div>
<div class="shadow-lg">Large shadow</div>
```

## Responsive Patterns

### Mobile-First Design
```blade
<!-- Stack on mobile, 2 columns on tablet, 3 on desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Items -->
</div>

<!-- Hidden on mobile, shown on tablet+ -->
<div class="hidden md:block">
    Desktop only content
</div>

<!-- Text sizes that scale -->
<h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl">
    Responsive heading
</h1>

<!-- Padding that scales -->
<div class="px-4 sm:px-6 lg:px-8">
    Responsive padding
</div>
```

## Common Patterns

### Hero Section
```blade
<section class="relative px-4 sm:px-6 lg:px-8 py-16 sm:py-24 lg:py-32">
    <div class="max-w-7xl mx-auto">
        <div class="text-center space-y-4">
            <h1 class="text-5xl font-bold text-neutral-900">Headline</h1>
            <p class="text-xl text-neutral-600">Subheading</p>
            <a href="#" class="btn btn-primary">CTA</a>
        </div>
    </div>
</section>
```

### Feature Grid
```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach($features as $feature)
        <div class="card-hover space-y-4">
            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                <svg><!-- Icon --></svg>
            </div>
            <h3 class="text-lg font-semibold">{{ $feature['title'] }}</h3>
            <p class="text-neutral-600">{{ $feature['description'] }}</p>
        </div>
    @endforeach
</div>
```

### Empty State
```blade
<div class="text-center py-16">
    <div class="w-16 h-16 mx-auto mb-4 bg-neutral-100 rounded-full flex items-center justify-center">
        <svg class="w-8 h-8 text-neutral-400"><!-- Icon --></svg>
    </div>
    <h3 class="text-lg font-semibold text-neutral-900 mb-2">No results</h3>
    <p class="text-neutral-600 mb-6">Try adjusting your filters</p>
    <a href="#" class="btn btn-secondary">Reset Filters</a>
</div>
```

## Tips

- Always use responsive breakpoints (sm, md, lg) for mobile-first design
- Keep spacing consistent using the Tailwind scale (4, 6, 8, 12, etc.)
- Use semantic colors (primary, error, success) for meaning
- Never remove focus states on interactive elements
- Test on actual mobile devices, not just browser dev tools
- Use animations sparingly and respect prefers-reduced-motion
