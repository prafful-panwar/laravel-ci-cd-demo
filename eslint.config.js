import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';
import prettierRecommended from 'eslint-plugin-prettier/recommended';
import globals from 'globals';

export default [
    {
        ignores: ['**/vendor/**', '**/node_modules/**', '**/public/**', '**/storage/**', '**/bootstrap/cache/**'],
    },
    js.configs.recommended,
    ...pluginVue.configs['flat/recommended'],
    prettierRecommended,
    {
        languageOptions: {
            globals: {
                ...globals.browser,
                ...globals.node,
            },
        },
    },
    {
        rules: {
            'vue/multi-word-component-names': 'off',
        },
    },
];
