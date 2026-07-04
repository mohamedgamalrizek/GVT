<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, BriefcaseBusiness, Building2, ClipboardCheck, FileText, LayoutGrid, Mail, Newspaper, Settings, Shield, Users } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
        permission: 'view dashboard',
    },
    {
        title: 'CRM',
        href: '/admin/crm',
        icon: BriefcaseBusiness,
        permission: 'manage assigned clients',
    },
    {
        title: 'Theses',
        href: '/admin/theses',
        icon: FileText,
        permission: 'manage theses',
    },
    {
        title: 'Page Sections',
        href: '/admin/page-sections',
        icon: BookOpen,
        permission: 'manage pages',
    },
    {
        title: 'Positions',
        href: '/admin/positions',
        icon: Building2,
        permission: 'manage positions',
    },
    {
        title: 'Developers',
        href: '/admin/developers',
        icon: Shield,
        permission: 'manage developers',
    },
    {
        title: 'Certifications',
        href: '/admin/certifications',
        icon: ClipboardCheck,
        permission: 'manage certifications',
    },
    {
        title: 'Blog',
        href: '/admin/blog',
        icon: Newspaper,
        permission: 'manage blog',
    },
    {
        title: 'Newsletter',
        href: '/admin/newsletter',
        icon: Mail,
        permission: 'export subscribers',
    },
    {
        title: 'Users',
        href: '/admin/users',
        icon: Users,
        permission: 'manage users',
    },
    {
        title: 'Settings',
        href: '/admin/settings',
        icon: Settings,
        permission: 'manage settings',
    },
];

const footerNavItems: NavItem[] = [];
const page = usePage();
const permissions = computed(() => page.props.auth.permissions ?? []);
const visibleNavItems = computed(() => mainNavItems.filter((item) => !item.permission || permissions.value.includes(item.permission)));
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="border-[#1a1712] bg-[#070707] text-white">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="visibleNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
