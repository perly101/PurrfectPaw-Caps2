<?php

namespace App\Exports;

use App\Models\User;

class UsersExport
{
    protected $users;
    
    /**
     * Create a new UsersExport instance.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $users
     * @return void
     */
    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * Get the headings for the CSV export
     * 
     * @return array
     */
    public function getHeadings(): array
    {
        return [
            'ID',
            'First Name',
            'Middle Name',
            'Last Name',
            'Email',
            'Phone',
            'Gender',
            'Birthday',
            'Role',
            'Registered At'
        ];
    }
    
    /**
     * Convert a user to an array for CSV export
     * 
     * @param  \App\Models\User  $user
     * @return array
     */
    public function userToArray($user): array
    {
        return [
            $user->id,
            $user->first_name,
            $user->middle_name,
            $user->last_name,
            $user->email,
            $user->phone_number,
            $user->gender,
            $user->birthday,
            $user->role,
            $user->created_at->format('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Generate CSV content from users
     * 
     * @return string
     */
    public function toCsv(): string
    {
        $output = fopen('php://temp', 'r+');
        
        // Add headers
        fputcsv($output, $this->getHeadings());
        
        // Add user data
        foreach ($this->users as $user) {
            fputcsv($output, $this->userToArray($user));
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}
