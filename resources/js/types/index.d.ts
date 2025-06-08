import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
}

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string | null;
    phone?: string | null;
    address?: string | null;
    city?: string | null;
    postal_code?: string | null;
    country: string;
    avatar_path?: string | null;
    is_active: boolean;
    is_admin: boolean;
    last_login_at?: string | null;
    preferred_language: string;
    preferences: Record<string, any>;
    current_organization_id?: number | null;
}

export interface PageProps {
    auth: {
        user: User | null;
    };
    [key: string]: any;
}

export interface BreadcrumbItemType {
    title: string;
    href?: string;
}
