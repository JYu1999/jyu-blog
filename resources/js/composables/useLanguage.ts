import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

export interface LanguageOption {
  value: string;
  label: string;
  flag: string;
}

export const availableLanguages: LanguageOption[] = [
  { value: 'en', label: 'English', flag: '🇺🇸' },
  { value: 'zh', label: '繁體中文', flag: '🇹🇼' },
  { value: 'zh-CN', label: '简体中文', flag: '🇨🇳' },
  { value: 'ja', label: '日本語', flag: '🇯🇵' },
  { value: 'vi', label: 'Tiếng Việt', flag: '🇻🇳' },
];

export function useLanguage() {
  const currentLang = ref<string>('en');
  const isBrowser = typeof window !== 'undefined';
  
  const updateLocale = (locale: string) => {
    currentLang.value = locale;
    if (isBrowser) {
      localStorage.setItem('locale', locale);
      document.documentElement.lang = locale;
      
      // 獲取當前路由
      const currentPath = window.location.pathname;
      const params = new URLSearchParams(window.location.search);
      params.set('locale', locale);
      
      // 使用 router.get 而不是直接重新載入頁面
      const url = `${currentPath}?${params.toString()}`;
      window.location.href = url;
    }
  };
  
  const initializeLanguage = () => {
    if (isBrowser) {
      const savedLocale = localStorage.getItem('locale');
      if (savedLocale) {
        currentLang.value = savedLocale;
        document.documentElement.lang = savedLocale;
      } else {
        // 使用瀏覽器默認語言或設置為默認值
        const browserLang = navigator.language;
        const matchedLang = availableLanguages.find(
          lang => browserLang.startsWith(lang.value)
        );
        
        if (matchedLang) {
          currentLang.value = matchedLang.value;
        }
        
        document.documentElement.lang = currentLang.value;
        localStorage.setItem('locale', currentLang.value);
      }
    }
  };
  
  onMounted(() => {
    initializeLanguage();
  });
  
  return {
    currentLang,
    availableLanguages,
    updateLocale,
    initializeLanguage
  };
}