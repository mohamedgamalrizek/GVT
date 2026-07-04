import type { Auth } from '@/types/auth';

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            siteSettings: {
                brand_name: string;
                short_name: string;
                slogan: string;
                logo_path: string | null;
                favicon_path: string | null;
                contact_email: string;
                contact_phone: string | null;
                contact_address: string | null;
                linkedin_url: string | null;
                x_url: string | null;
                instagram_url: string | null;
                default_seo_title: string;
                default_seo_description: string;
                default_seo_keywords: string | null;
            };
            [key: string]: unknown;
        };
    }
}

declare module 'vue' {
    interface ComponentCustomProperties {
        $inertia: typeof Router;
        $page: Page;
        $headManager: ReturnType<typeof createHeadManager>;
    }
}
