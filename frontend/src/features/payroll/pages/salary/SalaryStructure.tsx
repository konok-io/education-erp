import React, { useState } from 'react';

interface PayGrade {
  id: string;
  code: string;
  name: string;
  minSalary: number;
  maxSalary: number;
  basicPercent: number;
  houseRentPercent: number;
  medicalPercent: number;
  transportPercent: number;
  status: string;
}

interface SalaryStructure {
  id: string;
  employeeCode: string;
  employeeName: string;
  department: string;
  designation: string;
  grade: string;
  basicSalary: number;
  grossSalary: number;
  houseRent: number;
  medical: number;
  transport: number;
  otherAllowance: number;
  effectiveDate: string;
  status: string;
}

const payGrades: PayGrade[] = [
  { id: '1', code: 'G-01', name: 'Grade-01 (Staff)', minSalary: 25000, maxSalary: 40000, basicPercent: 40, houseRentPercent: 25, medicalPercent: 10, transportPercent: 10, status: 'active' },
  { id: '2', code: 'G-02', name: 'Grade-02 (Officer)', minSalary: 40000, maxSalary: 60000, basicPercent: 40, houseRentPercent: 25, medicalPercent: 10, transportPercent: 10, status: 'active' },
  { id: '3', code: 'G-03', name: 'Grade-03 (Senior Officer)', minSalary: 60000, maxSalary: 85000, basicPercent: 40, houseRentPercent: 25, medicalPercent: 10, transportPercent: 10, status: 'active' },
  { id: '4', code: 'G-04', name: 'Grade-04 (Manager)', minSalary: 85000, maxSalary: 120000, basicPercent: 40, houseRentPercent: 25, medicalPercent: 10, transportPercent: 10, status: 'active' },
  { id: '5', code: 'LEC', name: 'Lecturer', minSalary: 65000, maxSalary: 95000, basicPercent: 45, houseRentPercent: 25, medicalPercent: 10, transportPercent: 10, status: 'active' },
  { id: '6', code: 'ASTP', name: 'Assistant Professor', minSalary: 95000, maxSalary: 140000, basicPercent: 45, houseRentPercent: 25, medicalPercent: 10, transportPercent: 10, status: 'active' },
  { id: '7', code: 'ASCP', name: 'Associate Professor', minSalary: 140000, maxSalary: 180000, basicPercent: 45, houseRentPercent: 25, medicalPercent: 10, transportPercent: 10, status: 'active' },
  { id: '8', code: 'PROF', name: 'Professor', minSalary: 180000, maxSalary: 250000, basicPercent: 45, houseRentPercent: 25, medicalPercent: 10, transportPercent: 10, status: 'active' },
];

const salaryStructures: SalaryStructure[] = [
  { id: '1', employeeCode: 'EMP-001', employeeName: 'Rahim Ahmed', department: 'Academic', designation: 'Professor', grade: 'PROF', basicSalary: 200000, grossSalary: 320000, houseRent: 50000, medical: 20000, transport: 20000, otherAllowance: 30000, effectiveDate: '2026-01-01', status: 'active' },
  { id: '2', employeeCode: 'EMP-002', employeeName: 'Fatema Begum', department: 'Academic', designation: 'Associate Professor', grade: 'ASCP', basicSalary: 150000, grossSalary: 240000, houseRent: 37500, medical: 15000, transport: 15000, otherAllowance: 22500, effectiveDate: '2026-01-01', status: 'active' },
  { id: '3', employeeCode: 'EMP-003', employeeName: 'Kamal Hossain', department: 'IT', designation: 'Manager', grade: 'G-04', basicSalary: 100000, grossSalary: 160000, houseRent: 25000, medical: 10000, transport: 10000, otherAllowance: 15000, effectiveDate: '2026-01-01', status: 'active' },
  { id: '4', employeeCode: 'EMP-004', employeeName: 'Jamal Uddin', department: 'Admin', designation: 'Officer', grade: 'G-02', basicSalary: 50000, grossSalary: 80000, houseRent: 12500, medical: 5000, transport: 5000, otherAllowance: 7500, effectiveDate: '2026-01-01', status: 'active' },
];

const SalaryStructurePage: React.FC = () => {
  const [showGradeForm, setShowGradeForm] = useState(false);
  const [showStructureForm, setShowStructureForm] = useState(false);
  const [activeTab, setActiveTab] = useState<'grades' | 'structures'>('grades');

  const getStatusColor = (status: string) => {
    return status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Salary Structure</h1>
          <p className="text-gray-500">Pay Grades & Employee Salary Configuration</p>
        </div>
        <div className="flex gap-3">
          <button
            onClick={() => setShowStructureForm(true)}
            className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            + Assign Structure
          </button>
        </div>
      </div>

      {/* Tabs */}
      <div className="flex border-b border-gray-200">
        <button
          onClick={() => setActiveTab('grades')}
          className={`px-6 py-3 font-medium ${activeTab === 'grades' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'}`}
        >
          Pay Grades
        </button>
        <button
          onClick={() => setActiveTab('structures')}
          className={`px-6 py-3 font-medium ${activeTab === 'structures' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'}`}
        >
          Salary Structures
        </button>
      </div>

      {/* Pay Grades Tab */}
      {activeTab === 'grades' && (
        <div className="space-y-6">
          {/* Summary Cards */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div className="bg-white p-4 rounded-lg border border-gray-100">
              <p className="text-sm text-gray-500">Total Grades</p>
              <p className="text-2xl font-bold text-blue-600">{payGrades.length}</p>
            </div>
            <div className="bg-white p-4 rounded-lg border border-gray-100">
              <p className="text-sm text-gray-500">Min Salary</p>
              <p className="text-2xl font-bold text-green-600">৳25K</p>
            </div>
            <div className="bg-white p-4 rounded-lg border border-gray-100">
              <p className="text-sm text-gray-500">Max Salary</p>
              <p className="text-2xl font-bold text-purple-600">৳250K</p>
            </div>
            <div className="bg-white p-4 rounded-lg border border-gray-100">
              <p className="text-sm text-gray-500">Active Grades</p>
              <p className="text-2xl font-bold text-orange-600">{payGrades.filter(g => g.status === 'active').length}</p>
            </div>
          </div>

          {/* Pay Grades Table */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div className="p-4 border-b border-gray-100 flex items-center justify-between">
              <h3 className="font-semibold text-gray-900">Pay Grades</h3>
              <button
                onClick={() => setShowGradeForm(true)}
                className="text-sm text-blue-600 hover:text-blue-800"
              >
                + Add Grade
              </button>
            </div>
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade Name</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Min Salary</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Max Salary</th>
                  <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Basic %</th>
                  <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">HR %</th>
                  <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {payGrades.map((grade) => (
                  <tr key={grade.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 font-medium text-blue-600">{grade.code}</td>
                    <td className="px-6 py-4 text-gray-900">{grade.name}</td>
                    <td className="px-6 py-4 text-right text-gray-900">৳{grade.minSalary.toLocaleString()}</td>
                    <td className="px-6 py-4 text-right text-gray-900">৳{grade.maxSalary.toLocaleString()}</td>
                    <td className="px-6 py-4 text-center text-gray-600">{grade.basicPercent}%</td>
                    <td className="px-6 py-4 text-center text-gray-600">{grade.houseRentPercent}%</td>
                    <td className="px-6 py-4 text-center">
                      <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(grade.status)}`}>
                        {grade.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-center">
                      <button className="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Salary Structures Tab */}
      {activeTab === 'structures' && (
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <div className="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 className="font-semibold text-gray-900">Employee Salary Structures</h3>
            <div className="flex gap-2">
              <input
                type="text"
                placeholder="Search employee..."
                className="px-4 py-2 border border-gray-300 rounded-lg text-sm"
              />
            </div>
          </div>
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Basic</th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross</th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">House Rent</th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Medical</th>
                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {salaryStructures.map((structure) => (
                <tr key={structure.id} className="hover:bg-gray-50">
                  <td className="px-6 py-4">
                    <p className="font-medium text-gray-900">{structure.employeeName}</p>
                    <p className="text-sm text-gray-500">{structure.employeeCode}</p>
                  </td>
                  <td className="px-6 py-4 text-gray-600">{structure.department}</td>
                  <td className="px-6 py-4">
                    <span className="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                      {structure.grade}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-right text-gray-900">৳{structure.basicSalary.toLocaleString()}</td>
                  <td className="px-6 py-4 text-right font-medium text-blue-600">৳{structure.grossSalary.toLocaleString()}</td>
                  <td className="px-6 py-4 text-right text-gray-900">৳{structure.houseRent.toLocaleString()}</td>
                  <td className="px-6 py-4 text-right text-gray-900">৳{structure.medical.toLocaleString()}</td>
                  <td className="px-6 py-4 text-center">
                    <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(structure.status)}`}>
                      {structure.status}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-center">
                    <button className="text-blue-600 hover:text-blue-800 mr-2">View</button>
                    <button className="text-green-600 hover:text-green-800">Edit</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {/* Add Grade Modal */}
      {showGradeForm && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg">
            <div className="p-6 border-b border-gray-100">
              <h2 className="text-xl font-bold text-gray-900">Add Pay Grade</h2>
            </div>
            <div className="p-6 space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Grade Code</label>
                  <input type="text" className="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="G-05" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Grade Name</label>
                  <input type="text" className="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Grade-05" />
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Min Salary</label>
                  <input type="number" className="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="120000" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Max Salary</label>
                  <input type="number" className="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="180000" />
                </div>
              </div>
              <div className="grid grid-cols-4 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Basic %</label>
                  <input type="number" defaultValue={40} className="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">House %</label>
                  <input type="number" defaultValue={25} className="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Medical %</label>
                  <input type="number" defaultValue={10} className="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Transport %</label>
                  <input type="number" defaultValue={10} className="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                </div>
              </div>
            </div>
            <div className="p-6 border-t border-gray-100 flex justify-end gap-3">
              <button onClick={() => setShowGradeForm(false)} className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
              </button>
              <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Save Grade
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default SalaryStructurePage;
