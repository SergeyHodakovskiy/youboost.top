import { themeChange } from 'theme-change';

// Инициализация theme-change
themeChange();

// Функция для получения куки
const getCookie = (name) => {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
  return null;
};

// Функция для установки куки
const setCookie = (name, value, days) => {
  const expires = new Date(Date.now() + days * 864e5).toUTCString();
  document.cookie = `${name}=${value}; expires=${expires}; path=/`;
};

// Получение темы с сервера или fallback
const getTheme = async () => {
  try {
    const response = await axios.get('/profile/preferences/get');
    return response.data.theme || getCookie('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  } catch (error) {
    console.error('Ошибка загрузки темы с сервера:', error);
    return getCookie('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  }
};

// Установка темы с синхронизацией на сервер
const setTheme = async (theme) => {
  document.documentElement.setAttribute('data-theme', theme);
  setCookie('theme', theme, 365); // Всегда обновляем куки для консистентности
  try {
    await axios.post('/profile/preferences/set', { theme }, {
      headers: { 'Content-Type': 'application/json' },
    });
  } catch (error) {
    console.error('Ошибка сохранения темы на сервере:', error);
  }
};

// Инициализация темы
(async () => {
  const currentTheme = await getTheme();
  await setTheme(currentTheme);

  document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.querySelector('[data-theme-toggle]'); // Стандартный селектор theme-change
    if (themeToggle) {
      themeToggle.checked = currentTheme === 'dark';
      themeToggle.addEventListener('change', async () => {
        const newTheme = themeToggle.checked ? 'dark' : 'light';
        await setTheme(newTheme);
      });
    }
  });
})();