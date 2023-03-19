declare module 'v-mask' {
    import { DirectiveHook } from 'vue';

    interface VueMaskDirectiveType {
        bind: DirectiveHook;
        componentUpdated: DirectiveHook;
        unbind: DirectiveHook;
    }
    export const VueMaskDirective: VueMaskDirectiveType;
}