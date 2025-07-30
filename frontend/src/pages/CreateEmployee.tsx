// src/pages/CreateEmployee.tsx
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { toast } from 'react-toastify'
import Button from 'react-bootstrap/Button';
import Col from 'react-bootstrap/Col';
import Form from 'react-bootstrap/Form';
import InputGroup from 'react-bootstrap/InputGroup';
import Row from 'react-bootstrap/Row';

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
      <Form onSubmit={handleSubmit}>
        <Row className='mb-3'>
          <Form.Group as={Col} md="4">
            <Form.Label>First Name</Form.Label>
            <Form.Control name="name" placeholder="First Name" value={form.name} onChange={handleChange} required />
            <Form.Control.Feedback>Looks good!</Form.Control.Feedback>
          </Form.Group>
          <Form.Group as={Col} md="4">
            <Form.Label>Last Name</Form.Label>
            <Form.Control name="lastName" placeholder="Last Name" value={form.lastName} onChange={handleChange} required />
            <Form.Control.Feedback>Looks good!</Form.Control.Feedback>
          </Form.Group>          
          <Form.Group as={Col} md="4">
            <Form.Label>Email</Form.Label>
            <InputGroup hasValidation>
              <InputGroup.Text id="inputGroupPrepend">@</InputGroup.Text>
              <Form.Control name="email" type="email" placeholder="Email" value={form.email} onChange={handleChange} required />
              <Form.Control.Feedback>Looks good!</Form.Control.Feedback>
            </InputGroup>
          </Form.Group>          
          <Form.Group as={Col} md="4">
            <Form.Label>Position</Form.Label>
            <Form.Control name="position" placeholder="Position" value={form.position} onChange={handleChange} required />
            <Form.Control.Feedback>Looks good!</Form.Control.Feedback>
          </Form.Group>          
          <Form.Group as={Col} md="4">
            <Form.Label>Birht Date</Form.Label>
            <Form.Control name="birthDate" type="date" value={
              form.birthDate
            } onChange={handleChange} required />
            <Form.Control.Feedback>Looks good!</Form.Control.Feedback>
          </Form.Group>          
        </Row>
        <Button type="submit">Save</Button>
      </Form>

      {error && <p style={{ color: 'red' }}>{error}</p>}
      {success && <p style={{ color: 'green' }}>{success}</p>}
    </div>
  )
}

export default CreateEmployee
