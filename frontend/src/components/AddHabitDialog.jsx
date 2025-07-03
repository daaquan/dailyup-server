import React, { useState } from 'react';

export default function AddHabitDialog({ onAdd, onClose }) {
  const [title, setTitle] = useState('');

  return (
    <div style={{ position: 'fixed', top: 0, left: 0, right: 0, bottom: 0, background: 'rgba(0,0,0,0.3)', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
      <div style={{ background: '#fff', padding: '20px', borderRadius: '16px' }}>
        <h2>習慣を追加</h2>
        <input value={title} onChange={e => setTitle(e.target.value)} />
        <button onClick={() => onAdd(title)}>追加</button>
        <button onClick={onClose}>閉じる</button>
      </div>
    </div>
  );
}
