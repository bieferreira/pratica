import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/index.php',
});

export const login = async (email, password) => {
  const res = await api.post('', { email, password }, {
    params: { r: 'login', action: 'login' },
  });

  return res.data.token;
};

export const getTasks = async (token) => {
  const res = await api.get('', {
    params: { r: 'tasks' },
    headers: { Authorization: `Bearer ${token}` },
  });

  return res.data;
};