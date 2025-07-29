import { BrowserRouter, Routes, Route } from 'react-router-dom'
import Login from '../pages/Login'
import Register from '../pages/Register'
import Dashboard from '../pages/Dashboard'
import Employees from '../pages/Employees'
import Navbar from '../components/Navbar'
import CreateEmployee from '../pages/CreateEmployee'
import EditEmployee from '../pages/EditEmployee'

const AppRouter = () => {
  return (
    <BrowserRouter>
      <Navbar />
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/employees" element={<Employees />} />
        <Route path="/register" element={<Register />} />
        <Route path='/employees/create' element={<CreateEmployee />} />
        <Route path="/employees/edit/:id" element={<EditEmployee />} />
      </Routes>
    </BrowserRouter>
  )
}

export default AppRouter
