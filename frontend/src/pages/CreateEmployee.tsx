// src/pages/CreateEmployee.tsx
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { toast } from 'react-toastify'

const CreateEmployee = () => {
  const navigate = useNavigate()
  const [form, setForm] = useState({
    name: '',
    lastName: '',
    email: '',
    position: '',
    birthDate: '',
  })

  const [error, setError] = useState('')
  const [success, setSuccess] = useState('')

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setForm({ ...form, [e.target.name]: e.target.value })
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    const token = localStorage.getItem('token')
    if (!token) {
      navigate('/login')
      return
    }

    try {
      const response = await fetch('http://localhost:8000/api/employees', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + token,
        },
        body: JSON.stringify(form),
      })

      const data = await response.json()

      if (!response.ok) {
        toast.error('Error creating employee')
        setError(data.error || data.errors?.join(', ') || 'Error creating employee')
        return
      }

      toast.success('Employee created successfully!')
      setSuccess('Employee created successfully!')
      setForm({ name: '', lastName: '', email: '', position: '', birthDate: '' })

      setTimeout(() => navigate('/dashboard'), 1500)
    } catch (err) {
      setError('Error connecting to server')
    }
  }

  return (
    <div>
      <h2>Create Employee</h2>
      <form onSubmit={handleSubmit}>
        <input name="name" placeholder="First Name" value={form.name} onChange={handleChange} required />
        <input name="lastName" placeholder="Last Name" value={form.lastName} onChange={handleChange} required />
        <input name="email" type="email" placeholder="Email" value={form.email} onChange={handleChange} required />
        <input name="position" placeholder="Position" value={form.position} onChange={handleChange} required />
        <input name="birthDate" type="date" value={form.birthDate} onChange={handleChange} required />
        <button type="submit">Save</button>
      </form>

      {error && <p style={{ color: 'red' }}>{error}</p>}
      {success && <p style={{ color: 'green' }}>{success}</p>}
    </div>
  )
}

export default CreateEmployee
