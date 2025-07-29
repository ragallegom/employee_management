// src/pages/Dashboard.tsx
import { useEffect } from 'react'
import { useNavigate } from 'react-router-dom'

const Dashboard = () => {
  const navigate = useNavigate()

  useEffect(() => {
    const token = localStorage.getItem('token')
    if (!token) {
      navigate('/login')
    }
  }, [navigate])

  return (
    <div>
      <h2>Welcome to your Dashboard</h2>
      <p>Use the navigation menu to manage employees.</p>
    </div>
  )
}

export default Dashboard
