// http://msdn.microsoft.com/en-us/library/aa366770(VS.85).aspx

#include <windows.h>
#include <stdio.h>

void main(int argc, char *argv[])
{
  MEMORYSTATUSEX statex;

  statex.dwLength = sizeof (statex);

  GlobalMemoryStatusEx (&statex);

  /*
  printf ("ullTotalPhys:     %u\n", statex.ullTotalPhys);
  printf ("ullAvailPhys:     %u\n", statex.ullAvailPhys);
  printf ("ullTotalPageFile: %u\n", statex.ullTotalPageFile);
  printf ("ullAvailPageFile: %u\n", statex.ullAvailPageFile);
  printf ("ullTotalVirtual:  %u\n", statex.ullTotalVirtual);
  printf ("ullAvailVirtual:  %u\n", statex.ullAvailVirtual);
  */

  // display as serialized php array
  // with the same structure as win32_ps_stat_mem does
  printf("a:6:{");
    printf("s:10:\"total_phys\";d:%u;",     statex.ullTotalPhys);
    printf("s:10:\"avail_phys\";d:%u;",     statex.ullAvailPhys);
    printf("s:14:\"total_pagefile\";d:%u;", statex.ullTotalPageFile);
    printf("s:14:\"avail_pagefile\";d:%u;", statex.ullAvailPageFile);
    printf("s:13:\"total_virtual\";d:%u;",  statex.ullTotalVirtual);
    printf("s:13:\"avail_virtual\";d:%u;",  statex.ullAvailVirtual);
  printf("}");

}
