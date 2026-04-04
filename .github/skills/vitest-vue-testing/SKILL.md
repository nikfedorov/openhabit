---
name: vitest-vue-testing
description: "Use this skill for Vue component testing with Vitest and Vue Test Utils. Trigger whenever writing, editing, fixing, or refactoring Vue component tests — including mounting components, testing props/slots/emits, mocking dependencies, stubbing child components, and testing Inertia.js pages. Activate when the user mentions Vue tests, component tests, vitest, test:vue, or references resources/js/**/*.test.ts files. Covers: mount/shallowMount, wrapper API, vi.mock/vi.fn, Inertia page testing, coverage. Do not use for Pest PHP tests or backend testing."
license: MIT
metadata:
  author: openhabit
---

# Vitest + Vue Test Utils

## Setup

This project uses Vitest (via `vite-plus`) with `@vue/test-utils` for Vue component testing.

- Config: `vitest.config.ts`
- Test files: `resources/js/**/*.test.ts`
- Run: `vendor/bin/sail bun run test:vue`
- Coverage: 100% required (lines, functions, branches, statements)

## Creating Tests

Place test files alongside components with `.test.ts` suffix:

```
resources/js/pages/Dashboard.vue
resources/js/pages/Dashboard.test.ts
```

## Basic Test Structure

```typescript
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import MyComponent from './MyComponent.vue';

describe('MyComponent', () => {
    it('renders correctly', () => {
        const wrapper = mount(MyComponent);
        expect(wrapper.text()).toContain('Hello');
    });
});
```

## Mounting Components

### Full mount (renders children)

```typescript
import { mount } from '@vue/test-utils';

const wrapper = mount(Component, {
    props: { count: 5 },
    slots: { default: '<p>Slot content</p>' },
});
```

### Shallow mount (stubs children)

```typescript
import { mount } from '@vue/test-utils';

const wrapper = mount(Component, { shallow: true });
// or
import { shallowMount } from '@vue/test-utils';
const wrapper = shallowMount(Component);
```

### Selective stubbing with shallow mount

```typescript
const wrapper = mount(Component, {
    shallow: true,
    global: {
        stubs: { ChildToRender: false }, // opt-out from stubbing
    },
});
```

## Testing Props

```typescript
it('renders props', () => {
    const wrapper = mount(Component, {
        props: { title: 'Hello', count: 42 },
    });

    expect(wrapper.text()).toContain('Hello');
    expect(wrapper.props('count')).toBe(42);
});
```

## Testing Emitted Events

```typescript
it('emits update event', async () => {
    const wrapper = mount(Component);
    await wrapper.find('button').trigger('click');

    expect(wrapper.emitted('update')).toBeTruthy();
    expect(wrapper.emitted('update')![0]).toEqual(['new-value']);
});
```

## Testing Slots

```typescript
import { h } from 'vue';

const wrapper = mount(Component, {
    slots: {
        default: 'Default content',
        header: h('h1', {}, 'Header'),
        footer: AnotherComponent,
    },
});
```

## Mocking & Stubs

### Global mocks

```typescript
const wrapper = mount(Component, {
    global: {
        mocks: {
            $route: { params: { id: '1' } },
            $t: (text: string) => text,
        },
        stubs: {
            RouterLink: true,
            SomeChild: { template: '<div>Stub</div>' },
        },
    },
});
```

### Module mocking with vi.mock

```typescript
import { vi } from 'vitest';

vi.mock('@/services/api', () => ({
    fetchData: vi.fn().mockResolvedValue({ name: 'Test' }),
}));
```

### Function mocking with vi.fn

```typescript
const onClick = vi.fn();
const wrapper = mount(Component, {
    props: { onClick },
});

await wrapper.find('button').trigger('click');
expect(onClick).toHaveBeenCalledOnce();
```

## Testing Inertia.js Pages

Inertia pages use `usePage()` and `Head`. Mock them:

```typescript
import { mount } from '@vue/test-utils';
import { vi } from 'vitest';
import Dashboard from './Dashboard.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div><slot /></div>' },
    Link: { template: '<a><slot /></a>' },
    usePage: vi.fn(() => ({
        props: {
            auth: {
                user: {
                    id: '1',
                    name: 'Test User',
                    email: 'test@example.com',
                    telegram_id: null,
                    locale: 'en',
                    last_active_at: null,
                    created_at: '2025-01-01',
                    updated_at: '2025-01-01',
                },
            },
            name: 'OpenHabit',
        },
    })),
}));

describe('Dashboard', () => {
    it('renders user information', () => {
        const wrapper = mount(Dashboard);
        expect(wrapper.text()).toContain('Test User');
    });
});
```

## Wrapper API (key methods)

| Method | Description |
|--------|-------------|
| `wrapper.text()` | Text content |
| `wrapper.html()` | Rendered HTML |
| `wrapper.find(selector)` | Find DOM element |
| `wrapper.findComponent(Comp)` | Find child component |
| `wrapper.findAll(selector)` | Find all matching elements |
| `wrapper.exists()` | Check element exists |
| `wrapper.classes()` | CSS classes array |
| `wrapper.attributes()` | DOM attributes |
| `wrapper.props()` | Component props |
| `wrapper.emitted()` | Emitted events |
| `wrapper.trigger(event)` | Trigger DOM event |
| `wrapper.setValue(value)` | Set input value |
| `wrapper.vm` | Component instance |

## Vitest Assertions

```typescript
expect(value).toBe(expected);
expect(value).toEqual(expected);      // deep equality
expect(value).toBeTruthy();
expect(value).toContain('text');
expect(fn).toHaveBeenCalledOnce();
expect(fn).toHaveBeenCalledWith(arg);
expect(promise).resolves.toBe(value);
expect(promise).rejects.toThrow();
```

## Async Testing

```typescript
it('loads data', async () => {
    const wrapper = mount(Component);

    // Wait for DOM updates
    await wrapper.vm.$nextTick();

    // Or trigger and wait
    await wrapper.find('button').trigger('click');

    expect(wrapper.text()).toContain('Loaded');
});
```

## Common Pitfalls

- Always `await` trigger/setValue calls before asserting.
- Use `vi.mock()` at the top level of the test file (hoisted automatically).
- When testing Inertia pages, mock `@inertiajs/vue3` before importing the component.
- Use `wrapper.vm.$nextTick()` to wait for reactive updates.
- Prefer `mount()` for integration tests, `shallowMount()` for unit tests.
- Keep test files colocated with their component files.
