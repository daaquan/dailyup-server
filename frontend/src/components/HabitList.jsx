import React from 'react';

export default function HabitList({ habits, onDelete, onToggle }) {
  return (
    <div>
      {habits.map(h => (
        <div key={h.id} style={{ background: '#fff', padding: '10px', margin: '10px 0', borderRadius: '16px', boxShadow: '0 2px 6px rgba(0,0,0,0.1)' }}>
          <label>
            <input type="checkbox" checked={h.checked} onChange={() => onToggle(h.id)} />
            <span style={{ marginLeft: '8px' }}>{h.title}</span>
          </label>
          <button style={{ marginLeft: '10px' }} onClick={() => onDelete(h.id)}>削除</button>
        </div>
      ))}
    </div>
  );
}
