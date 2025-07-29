// src/components/Navbar.tsx
import { Link, useNavigate } from 'react-router-dom'

const Navbar = () => {
  const navigate = useNavigate()

  const handleLogout = () => {
    localStorage.removeItem('token')
    navigate('/login')
  }

  return (
    <nav>
      <Link to="/dashboard">Dashboard</Link> |{' '}
      <Link to="/employees">Employees</Link> |{' '}
      <button onClick={handleLogout}>Logout</button>
    </nav>
  )
}

export default Navbar
