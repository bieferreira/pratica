import React, { useEffect, useState } from 'react';
import { login, getTasks, createTask, updateTask, deleteTask } from './api';
import TaskForm from './components/TaskForm';
import TaskList from './components/TaskList';

function App() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [token, setToken] = useState('');
  const [tasks, setTasks] = useState([]);
const [selectedTask, setSelectedTask] = useState(null);

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

  const loadTasks = async () => {
      const res = await getTasks();
      setTasks(res.data);
    };
  
    useEffect(() => {
      loadTasks();
    }, []);
  
  const handleSave = async task => {
      if (task.id) {
        await updateTask(task.id, task);
      } else {
        await createTask(task);
      }
      loadTasks();
    };
  
    const handleDelete = async id => {
      await deleteTask(id);
      loadTasks();
    };
  
  return (
    <div className="App">
      {!token && (
        <>
          <div className="container py-4">
            <div className="mb-3">
              <h1>Login</h1>
              <label className="form-label">Usuário</label>
              <input
                className="form-control"
                placeholder="Email"
                value={email}
                onChange={e => setEmail('joao@email.com')}
              />
              <label className="form-label">Senha</label>
              <input
                className="form-control"
                placeholder="Senha"
                type="password"
                value={password}
                onChange={e => setPassword('123456')}
              />
              <br />
              <div className="d-flex justify-content-center">
                <button className="btn btn-primary" onClick={handleLogin}>Entrar</button>
              </div>
            </div>
          </div>
        </>
      )}

      {token && (
        <div className="container py-4">
          <h1 className="mb-4">Gerenciador de Tarefas</h1>
          <TaskForm onSave={handleSave} selectedTask={selectedTask} clearSelected={() => setSelectedTask(null)} />
          <TaskList tasks={tasks} onEdit={setSelectedTask} onDelete={handleDelete} />
        </div>
      )}
    </div>
  );
}

export default App;