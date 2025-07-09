import React, { useState } from 'react';
import { login, getTasks } from './api';

function App() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [token, setToken] = useState('');
  const [tasks, setTasks] = useState([]);

  const handleLogin = async () => {
    
    
    // const t = await login(email, password);
    // setToken(t);
    try {
      const t = await login(email, password);
      setToken(t);
      console.log('Token:', t);

      const lista = await getTasks(t);
      console.log('Token:', t);
      setTasks(lista);

    } catch (error) {
      if (error.response) {
        alert('Erro da API: ' + error.response.data.error);
      } else {
        alert('Erro de rede ou inesperado');
      }
    }

  };

  return (
    <div className="App">
      <h1>Login</h1>
      <input placeholder="Email" value={email} onChange={e => setEmail('joao@email.com')} />
      <input placeholder="Senha" type="password" value={password} onChange={e => setPassword('123456')} />
      <button onClick={handleLogin}>Entrar</button>

      {token && (
        <>
          <h2>Tarefas</h2>
          <ul>
            {tasks.map(t => (
              <li key={t.id}>{t.title} - {t.status}</li>
            ))}
          </ul>
        </>
      )}
    </div>
  );
}

export default App;