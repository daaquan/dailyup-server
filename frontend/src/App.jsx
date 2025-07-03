import React, { useState, useEffect } from 'react';
import axios from 'axios';
import HabitList from './components/HabitList';
import AddHabitDialog from './components/AddHabitDialog';

export default function App() {
  const [habits, setHabits] = useState([]);
  const [showDialog, setShowDialog] = useState(false);

  useEffect(() => {
    axios.get('/api/habits').then(r => setHabits(r.data));
  }, []);

  const addHabit = title => {
    axios.post('/api/habits', { title }).then(r => setHabits([...habits, r.data]));
  };

  const deleteHabit = id => {
    axios.delete(`/api/habits/${id}`).then(() => setHabits(habits.filter(h => h.id !== id)));
  };

  const toggleHabit = id => {
    axios.patch(`/api/habits/${id}/toggle`).then(r => {
      setHabits(habits.map(h => (h.id === id ? r.data : h)));
    });
  };

  return (
    <div>
      <h1 style={{ textAlign: 'center', fontWeight: '700', fontSize: '2rem' }}>DailyUp</h1>
      <button onClick={() => setShowDialog(true)}>+ 習慣を追加</button>
      <HabitList habits={habits} onDelete={deleteHabit} onToggle={toggleHabit} />
      {showDialog && <AddHabitDialog onAdd={title => { addHabit(title); setShowDialog(false); }} onClose={() => setShowDialog(false)} />}
    </div>
  );
}
