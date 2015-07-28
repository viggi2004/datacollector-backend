Hi {$contact_person_name},
Citibytes Id: {$business_id}
Company Name: {$business_name}
{if isset($chain_name)}
Chain Name: {$chain_name}
{/if}
Address: {$address}
City: {$city} - {$pincode}
State: {$state}
{if isset($landmark)}
Landmark: {$landmark}
{/if}
{if isset($mobile_number)}
MobileNumber: {$mobile_number}
{/if}
{if isset($landline_number)}
LandlineNumber: {$landline_number}
{/if}
{if isset($email)}
Email: {$email}
{/if}
{if isset($website)}
Website: {$website}
{/if}
If the above information is correct, please enter your OTP {$otp}.
