export type User = {
    id: string;
    telegram_id: string | null;
    name: string | null;
    email: string | null;
    locale: string | null;
    last_active_at: string | null;
    created_at: string;
    updated_at: string;
};

export type Auth = {
    user: User;
};

export type Habit = {
    id: number;
    name: string;
    description: string | null;
    iterations_required: number;
    is_completed: boolean;
    current_iteration: number;
    sort_order: number;
};

export type ActivityDay = {
    date: string;
    percentage: number;
    completed: number;
    total: number;
    intensity: number;
};
